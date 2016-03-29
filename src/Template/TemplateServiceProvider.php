<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template;

use Hubzero\Base\ServiceProvider;

/**
 * Component loader service provider
 */
class TemplateServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['template.loader'] = function ($app)
		{
			$options = [
				'path_app'  => PATH_APP . DS . 'templates',
				'path_core' => PATH_CORE . DS . 'templates',
				'style'     => 0,
				'lang'      => ''
			];

			if ($app->isAdmin())
			{
				$options['style'] = \User::getParam('admin_style', $options['style']);
			}

			return new Loader($app, $options);
		};

		$this->app['template'] = function ($app)
		{
			$loader = $app['template.loader'];

			if ($app->isSite() && $app->has('menu'))
			{
				$menu = $app['menu'];

				if (!($item = $menu->getActive()))
				{
					$item = $menu->getItem($app['request']->getInt('Itemid', 0));
				}

				if (is_object($item))
				{
					$loader->setStyle($item->template_style_id);
				}

				if ($app->has('language.filter'))
				{
					$loader->setLang($app['language']->getTag());
				}
			}

			if ($style = $app['request']->getVar('templateStyle', 0))
			{
				$loader->setStyle($style);
			}

			return $loader->load();
		};
	}
}
