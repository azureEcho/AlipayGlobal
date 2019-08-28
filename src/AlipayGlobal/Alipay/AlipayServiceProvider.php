<?php
namespace AlipayGlobal\Alipay;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class AlipayServiceProvider extends ServiceProvider
{

	/**
	 * boot process
	 */
	public function boot()
	{
		$this->setupConfig();
	}

	/**
	 * Setup the config.
	 *
	 * @return void
	 */
	protected function setupConfig()
	{
		$source_config = realpath(__DIR__ . '/../../config/config.php');
		$source_mobile = realpath(__DIR__ . '/../../config/mobile.php');
		$source_web = realpath(__DIR__ . '/../../config/web.php');
		if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
			$this->publishes([
				$source_config => config_path('global-alipay.php'),
				$source_mobile => config_path('global-alipay-mobile.php'),
				$source_web => config_path('global-alipay-web.php'),
			]);
		} elseif ($this->app instanceof LumenApplication) {
			$this->app->configure('global-alipay');
			$this->app->configure('global-alipay-mobile');
			$this->app->configure('global-alipay-web');
		}
		
		$this->mergeConfigFrom($source_config, 'global-alipay');
		$this->mergeConfigFrom($source_mobile, 'global-alipay-mobile');
		$this->mergeConfigFrom($source_web, 'global-alipay-web');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		
		$this->app->bind('alipayglobal.mobile', function ($app)
		{
			$alipay = new Mobile\SdkPayment();

			$alipay->setPartner($app->config->get('global-alipay.partner_id'))
				->setSignType($app->config->get('global-alipay-mobile.sign_type'))
				->setPrivateKeyPath($app->config->get('global-alipay-mobile.private_key_path'))
				->setPublicKeyPath($app->config->get('global-alipay-mobile.public_key_path'))
				->setNotifyUrl($app->config->get('global-alipay-mobile.notify_url'));

			return $alipay;
		});

		$this->app->bind('alipayglobal.web', function ($app)
		{
			$alipay = new Web\SdkPayment();

			$alipay->setPartner($app->config->get('global-alipay.partner_id'))
				->setKey($app->config->get('global-alipay-web.key'))
				->setSignType($app->config->get('global-alipay-web.sign_type'))
				->setNotifyUrl($app->config->get('global-alipay-web.notify_url'))
				->setReturnUrl($app->config->get('global-alipay-web.return_url'))
				->setReferUrl(($app->config->get('global-alipay.refer_url')));
			return $alipay;
		});

		$this->app->bind('alipayglobal.wap', function ($app)
		{
			$alipay = new Wap\SdkPayment();

			$alipay->setPartner($app->config->get('global-alipay.partner_id'))
			->setKey($app->config->get('global-alipay-web.key'))
			->setSignType($app->config->get('global-alipay-web.sign_type'))
			->setNotifyUrl($app->config->get('global-alipay-web.notify_url'))
			->setReturnUrl($app->config->get('global-alipay-web.return_url'))
			->setReferUrl(($app->config->get('global-alipay.refer_url')));

			return $alipay;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'alipayglobal.mobile',
			'alipayglobal.web',
			'alipayglobal.wap',
		];
	}
}
