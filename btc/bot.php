<?php
	require_once('api.php');
	
	//TODO: Add exposure rating. +1 for each order.
	//TODO: Add in freeze reset. Destroy and sell all sell orders when exposure is frozen.
	//TODO: I may have some BTC fractions left over. Account for them in create_sells.

	class BtcBot
	{
		public static $min=1;
		public static $max=1000;
		public static $usd_threshold=10;
		public static $btc_threshold=0.00000001;
		public static $fee=0.002;
		public static $btc_limit=0.01;
		
		public $btc_info;
		public $account_info;
		public $transactions;
		public $orders;
		
		private $api;
		
		public function __construct($api_key, $api_secret)
		{
			$this->login($api_key, $api_secret);
			unset($api_key, $api_secret);
			if (!$this->api)
			{
				$this->email_alert('login');
				return;
			}

			$this->refresh();

			if ($this->account_info['funds']['btc'])
			{
				$this->create_sells();
			}
			
			if ($this->check_bump())
			{
				$this->destroy_buys();
				$this->create_buys();
			}
			
			$this->refresh();
			
			return;
		}
		
		public function refresh()
		{
			$this->btc_info=$this->get_btc_info();
			$this->account_info=$this->get_account_info();
			$this->transactions=$this->get_transactions();
			$this->orders=$this->get_orders();
			
			return;
		}
		
		public function login($api_key, $api_secret)
		{
			$this->api=new BTCeAPI($api_key, $api_secret);
			unset($api_key, $api_secret);
			
			return;
		}
		
		public function email_alert($where)
		{
			@mail(EMAIL, 'BTC Bot Fail', 'on: '.$where);
			
			return;
		}
		
		public function get_btc_info()
		{
			$results=$this->api->getPairTicker('btc_usd');
			return $results['ticker'];
		}
		
		public function get_account_info()
		{
			$results=$this->api->apiQuery('getInfo');
			return $results['return'];
		}
		
		public function get_transactions()
		{
			$results=$this->api->apiQuery('TradeHistory');
			return $results['return'];
		}
		
		public function get_orders()
		{
			$results=$this->api->apiQuery('ActiveOrders');
			if (isset($results['return']))
			{
				return $results['return'];
			}
			return false;
		}
		
		public function create_sells()
		{
			$this->refresh();
			$btc_amount=$this->account_info['funds']['btc'];

			foreach ($this->transactions as $transaction)
			{
				if (!$this->account_info['funds']['btc'])
				{
					break;
				}
				
				if ($transaction['pair']=='btc_usd' && $transaction['type']=='buy')
				{
					$amount=$transaction['amount']-($transaction['amount']*static::$fee);
					$result=$this->create_sell($amount, $transaction['rate']+static::$usd_threshold);
					
					if (!$result['success'])
					{
						break;
					}
				}

				$this->account_info=$this->get_account_info();
			}
			
			return;
		}
		
		public function create_sell($amount, $price)
		{
			return $this->api->makeOrder($amount, 'btc_usd', 'sell', $price);
		}
		
		public function check_bump()
		{
			$this->refresh();
			$largest=static::$min;
			
			if ($this->orders)
			{
				foreach ($this->orders as $order)
				{
					if ($order['pair']=='btc_usd' && $order['type']=='buy' && $order['rate']>$largest)
					{
						$largest=$order['rate'];
					}
				}
			}
			
			$price=round($this->btc_info['sell']/static::$usd_threshold)*static::$usd_threshold;
			if (($largest+static::$usd_threshold)<$price)
			{
				return true;
			}
			return false;
		}
		
		public function destroy_buys()
		{
			$this->refresh();
			
			if ($this->orders)
			{
				foreach ($this->orders as $order_id=>$order)
				{
					if ($order['pair']=='btc_usd' && $order['type']=='buy')
					{
						$this->api->apiQuery('CancelOrder', array('order_id'=>$order_id));
					}
				}
			}
			
			return;
		}
		
		public function create_buys()
		{
			$this->refresh();
			$price=(round($this->btc_info['sell']/static::$usd_threshold)*static::$usd_threshold)-static::$usd_threshold;

			for ($usd=$this->account_info['funds']['usd']; $usd>static::$usd_threshold && $price>static::$min; $price-=static::$usd_threshold)
			{
				$amount=($usd/2)/$price;
				if (!($amount>=static::$btc_limit))
				{
					$amount=$usd/$price;
				}
				$result=$this->create_buy($amount, $price);
				
				if (!$result['success'])
				{
					break;
				}
				
				$usd/=2;
			}
			
			return;
		}
		
		public function create_buy($amount, $price)
		{
			$amount=floor($amount/static::$btc_threshold)*static::$btc_threshold;
			return $this->api->makeOrder($amount, 'btc_usd', 'buy', $price);
		}
	}
?>