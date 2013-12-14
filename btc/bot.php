<?php
	require_once('api.php');

	class BtcBot
	{
		public static $min=0;
		public static $max=1000;
		public static $usd_threshold=10;
		public static $btc_threshold=0.0001;
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
			return $results['return'];
		}
		
		public function create_sells()
		{
			foreach ($this->tranactions as $transaction)
			{
				$this->create_sell();

				$this->account_info=$this->get_account_info();
				if (!$this->account_info['funds']['btc'])
				{
					break;
				}
			}
			
			return;
		}
		
		public function create_sell()
		{
			return;
		}
		
		public function check_bump()
		{
			return;
		}
		
		public function destroy_buys()
		{
			return;
		}
		
		public function create_buys()
		{
			return;
		}
		
		public function create_buy()
		{
			return;
		}
	}
?>