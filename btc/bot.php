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
		
		public $transactions;
		public $price;
		public $buys;
		public $sells;
		public $usd_ammont;
		public $btc_ammount;
		
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
			
			$this->btc_ammount=$this->check_btc_ammount();
			if ($this->btc_ammount)
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
			$this->transactions=$this->check_transactions();
			$this->price=$this->check_price();
			$this->buys=$this->check_buys();
			$this->sells=$this->check_sells();
			$this->usd_ammont=$this->check_usd_ammount();
			$this->btc_ammount=$this->check_btc_ammount();
			
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
		
		public function check_btc_ammount()
		{
			return;
		}
		
		public function check_transactions()
		{
			return;
		}
		
		public function create_sells()
		{
			foreach ($this->tranactions as $transaction)
			{
				$this->create_sell();

				$this->btc_ammount=$this->check_btc_ammount();
				if (!$this->btc_ammount)
				{
					break;
				}
			}
			
			return;
		}
		
		public function check_sells()
		{
			
		}
		
		public function create_sell()
		{
			return;
		}
		
		public function check_price()
		{
			return;
		}
		
		public function check_buys()
		{
			return;
		}
		
		public function check_bump()
		{
			
		}
		
		public function destroy_buys()
		{
			return;
		}
		
		public function check_usd_ammount()
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