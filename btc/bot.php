<?php
	require_once('api.php');

	class BtcBot
	{
		public static $min;
		public static $max;
		public static $usd_threshold;
		public static $btc_threshold;
		public static $fee;
		public static $btc_limit;
		
		public $transactions;
		public $price;
		public $buys;
		public $sells;
		public $usd_ammont;
		public $btc_ammount;
		
		private $api;
		
		public function __construct()
		{
			$this->min=0;
			$this->max=1000;
			$this->usd_threshold=10;
			$this->btc_threshold=0.0001;
			$this->fee=0.2/100;
			$this->btc_limit=0.01;
			
			$this->api=$this->login('login');
			if (!$this->api)
			{
				email_alert();
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
		
		public function login($where)
		{
			mail(EMAIL, 'BTC Bot Fail', 'on: '.$where);
			
			return;
		}
		
		public function email_alert()
		{
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