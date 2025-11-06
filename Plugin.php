<?php
namespace TypechoPlugin\Sitemap;

use Typecho\Plugin\Exception;
use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Utils\Helper;

if (!defined('__TYPECHO_ROOT_DIR__')) {
  exit;
}

/**
 * 网站 Sitemap 生成器
 * 
 * @package Sitemap
 * @author Hanny, 禾令奇, uy/sun
 * @version 1.0.3
 * @since 1.2.0
 * @link https://github.com/he0119/typecho-sitemap
 *
 * 历史版本
 * version 1.1.0 at 2025-11-06
 * 适配 Typecho 1.2.0，使用命名空间和新式类名
 *
 * version 1.0.3 at 2017-03-28
 * 【禾令奇】www.helingqi.com 修改增加标签链接，修改页面权重分级。
 *
 * version 1.0.1 at 2010-01-02
 * 修改自定义静态链接时错误的Bug
 *
 * version 1.0.0 at 2010-01-02
 * Sitemap for Google
 * 生成文章和页面的Sitemap
 *
 */
class Plugin implements PluginInterface
{
  /**
   * 激活插件方法,如果激活失败,直接抛出异常
   * 
   * @access public
   * @return string
   * @throws Exception
   */
  public static function activate()
  {
    Helper::addRoute('sitemap', '/sitemap.xml', 'Sitemap_Action', 'action');
    return _t('插件已激活');
  }

  /**
   * 禁用插件方法,如果禁用失败,直接抛出异常
   * 
   * @static
   * @access public
   * @return string
   * @throws Exception
   */
  public static function deactivate()
  {
    Helper::removeRoute('sitemap');
    return _t('插件已禁用');
  }

  /**
   * 获取插件配置面板
   * 
   * @access public
   * @param Form $form 配置面板
   * @return void
   */
  public static function config(Form $form)
  {
  }

  /**
   * 个人用户的配置面板
   * 
   * @access public
   * @param Form $form
   * @return void
   */
  public static function personalConfig(Form $form)
  {
  }

}
