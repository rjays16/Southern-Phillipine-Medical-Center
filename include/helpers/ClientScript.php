<?php

/**
 * ClientScript.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Helpers;
use Segworks\HIS\Base\ErrorException;

/**
 * ClientScript is a global-scope manager for JS and CSS scripts.
 *
 * @package include.helpers
 */
class ClientScript extends Helper
{
    /**
     * Renders the script at the HEAD section
     */
    const POS_HEAD = 0;
    /**
     * Renders the script at the beginning of the BODY section
     */
    const POS_BEGIN = 1;
    /**
     * Renders the script before the end of the BODY section
     */
    const POS_END = 2;
    /**
     * Renders the script on the onLoad function of the window object
     */
    const POS_LOAD = 3;
    /**
     * Renders the script inside the jQuery.ready function
     */
    const POS_READY = 4;

    protected $hasScripts = false;
    protected $css = array();
    protected $cssFiles = array();
    protected $scripts = array();
    protected $scriptFiles = array();

    /**
     * Initialization routine for the
     */
    protected function init()
    {
        $this->config = ArrayHelper::merge(array(
            //
            'defaultScriptFilePosition' => self::POS_HEAD,
            'enableJavaScript' => true,
            'packages' => array(
                'jquery' => array(
                    'js' => array(
                        'js/jquery/jquery-1.10.2.js'
                    )
                ),
                'jquery-ui' => array(
                    'js' => array(
                        'js/jquery/ui/jquery-ui-1.9.1.js'
                    ),
                    'css' => array(
                        'js/jquery/themes/seg-ui/jquery.ui.all.css'
                    ),
                    'depends' => array('jquery')
                ),
                'foundation' => array(
                    'css' => array(
                        'css/normalize.css',
                        'css/foundation/foundation.min.css',
                        'css/doc.css'
                    ),
                    'js' => array(
                        array(
                            'path' => 'js/foundation/foundation.min.js',
                            'position' => self::POS_END
                        )
                    ),
                    'depends' => array('jquery')
                ),
                'overlib' => array(
                    'js' => array(
                        'js/overlibmws/iframecontentmws.js',
                        'js/overlibmws/overlibmws.js',
                        'js/overlibmws/overlibmws_filter.js',
                        'js/overlibmws/overlibmws_overtwo.js',
                        'js/overlibmws/overlibmws_scroll.js',
                        'js/overlibmws/overlibmws_shadow.js',
                        'js/overlibmws/overlibmws_modal.js',
                    )
                )
            )
        ), $this->config);
    }

    /**
     * Renders the registered scripts.
     *
     * @param \smarty_care $smarty The Smarty Template object used by the application
     */
    public static function render($smarty)
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */

        if(!$instance->hasScripts)
            return;

        // $instance->renderCoreScripts();

        // if(!empty($instance->scriptMap))
        //     $instance->remapScripts();

        $instance->unifyScripts();
        $instance->renderHead($smarty);
        if(self::getConfig('enableJavaScript'))
        {
            $instance->renderBodyBegin($smarty);
            $instance->renderBodyEnd($smarty);
        }
    }

    /**
     *
     * @param string $packageName
     */
    public static function registerPackage($packageName)
    {
        global $root_path;
        $packages = self::getConfig('packages');
        if (isset($packages[$packageName])) {
            $package = $packages[$packageName];
            if (is_array($package['js'])) {
                foreach ($package['js'] as $js) {
                    $pos = null;
                    if (is_array($js)) {
                        $pos = $js['position'];
                        $js = $js['path'];
                    }
                    if (is_callable($js)) {
                        // Closure
                        self::registerScriptFile($js(), $pos);
                    } else {
                        // Treat as String
                        $path = $root_path.$js;
                        self::registerScriptFile($path, $pos);
                    }
                }
            }

            if (is_array($package['css'])) {
                foreach ($package['css'] as $css) {
                    if (is_callable($css)) {
                        // Closure
                        self::registerCssFile($css());
                    } else {
                        // Treat as String
                        self::registerCssFile($root_path.$css);
                    }
                }
            }

            if(!empty($package['depends']))
            {
                foreach($package['depends'] as $p) {
                    self::registerPackage($p);
                }
            }
        }
    }

    /**
     * Registers a javascript file.
     * @param string $url URL of the javascript file
     * @param integer $position the position of the JavaScript code. Valid values include the following:
     * <ul>
     * <li>ClientScript::POS_HEAD : the script is inserted in the head section right before the title element.</li>
     * <li>ClientScript::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
     * <li>ClientScript::POS_END : the script is inserted at the end of the body section.</li>
     * </ul>
     * @param array $htmlOptions additional HTML attributes
     * @return ClientScript the ClientScript object itself (to support method chaining, available since version 1.1.5).
     */
    public static function registerScriptFile($url,$position=null,array $htmlOptions=array())
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        if($position===null)
            $position=self::getConfig('defaultScriptFilePosition');
        $instance->hasScripts=true;
        if(empty($htmlOptions))
            $value=$url;
        else
        {
            $value=$htmlOptions;
            $value['src']=$url;
        }
        $instance->scriptFiles[$position][$url]=$value;
        return $instance;
    }

    /**
     * Registers a piece of javascript code.
     * @param string $id ID that uniquely identifies this piece of JavaScript code
     * @param string $script the javascript code
     * @param integer $position the position of the JavaScript code. Valid values include the following:
     * <ul>
     * <li>CClientScript::POS_HEAD : the script is inserted in the head section right before the title element.</li>
     * <li>CClientScript::POS_BEGIN : the script is inserted at the beginning of the body section.</li>
     * <li>CClientScript::POS_END : the script is inserted at the end of the body section.</li>
     * <li>CClientScript::POS_LOAD : the script is inserted in the window.onload() function.</li>
     * <li>CClientScript::POS_READY : the script is inserted in the jQuery's ready function.</li>
     * </ul>
     * @param array $htmlOptions additional HTML attributes
     * Note: HTML attributes are not allowed for script positions "CClientScript::POS_LOAD" and "CClientScript::POS_READY".
     * @return ClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     */
    public static function registerScript($id,$script,$position=null,array $htmlOptions=array())
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        if($position===null)
            $position=self::getConfig('defaultScriptFilePosition');
        $instance->hasScripts=true;
        if(empty($htmlOptions))
            $scriptValue=$script;
        else
        {
            if($position==self::POS_LOAD || $position==self::POS_READY)
                throw new ErrorException('Script HTML options are not allowed for "CClientScript::POS_LOAD" and "CClientScript::POS_READY".');
            $scriptValue=$htmlOptions;
            $scriptValue['content']=$script;
        }
        $instance->scripts[$position][$id]=$scriptValue;

        // if($position===self::POS_READY || $position===self::POS_LOAD)
        //     $instance->registerCoreScript('jquery');

        return $instance;
    }

    /**
     * Registers a piece of CSS code.
     * @param string $id ID that uniquely identifies this piece of CSS code
     * @param string $css the CSS code
     * @param string $media media that the CSS code should be applied to. If empty, it means all media types.
     * @return ClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     */
    public static function registerCss($id,$css,$media='')
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        $instance->hasScripts=true;
        $instance->css[$id]=array($css,$media);
        return $instance;
    }

    /**
     * Registers a CSS file
     * @param string $url URL of the CSS file
     * @param string $media media that the CSS file should be applied to. If empty, it means all media types.
     * @return ClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
     */
    public static function registerCssFile($url,$media='')
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        $instance->hasScripts=true;
        $instance->cssFiles[$url]=$media;
        return $instance;
    }


    /**
     * Removes duplicated scripts from {@link scriptFiles}.
     */
    protected function unifyScripts()
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        if(!self::getConfig('enableJavaScript'))
            return;
        $map=array();
        if(isset($instance->scriptFiles[self::POS_HEAD]))
            $map=$instance->scriptFiles[self::POS_HEAD];

        if(isset($instance->scriptFiles[self::POS_BEGIN]))
        {
            foreach($instance->scriptFiles[self::POS_BEGIN] as $scriptFile=>$scriptFileValue)
            {
                if(isset($map[$scriptFile]))
                    unset($instance->scriptFiles[self::POS_BEGIN][$scriptFile]);
                else
                    $map[$scriptFile]=true;
            }
        }

        if(isset($instance->scriptFiles[self::POS_END]))
        {
            foreach($instance->scriptFiles[self::POS_END] as $key=>$scriptFile)
            {
                if(isset($map[$key]))
                    unset($instance->scriptFiles[self::POS_END][$key]);
            }
        }
    }

    /**
     * Inserts the scripts in the head section.
     *
     * @param \smarty_care $smarty
     */
    protected function renderHead($smarty)
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        $html='';
        // foreach($instance->metaTags as $meta)
        //     $html.=HtmlHelper::metaTag($meta['content'],null,null,$meta)."\n";
        // foreach($instance->linkTags as $link)
        //     $html.=HtmlHelper::linkTag(null,null,null,null,$link)."\n";
        foreach($instance->cssFiles as $url=>$media)
            $html.=HtmlHelper::cssFile($url,$media)."\n";
        foreach($instance->css as $css)
            $html.=HtmlHelper::css($css[0],$css[1])."\n";
        if(self::getConfig('enableJavaScript'))
        {
            if(isset($instance->scriptFiles[self::POS_HEAD]))
            {
                foreach($instance->scriptFiles[self::POS_HEAD] as $scriptFileValueUrl=>$scriptFileValue)
                {
                    if(is_array($scriptFileValue))
                        $html.=HtmlHelper::scriptFile($scriptFileValueUrl,$scriptFileValue)."\n";
                    else
                        $html.=HtmlHelper::scriptFile($scriptFileValueUrl)."\n";
                }
            }

            if(isset($instance->scripts[self::POS_HEAD]))
                $html.=$instance->renderScriptBatch($this->scripts[self::POS_HEAD]);

        }

        if($html!=='')
        {
            $smarty->assign('ClientScriptHead', $html);
        }
    }

    /**
     * Composes script HTML block from the given script values,
     * attempting to group scripts at single 'script' tag if possible.
     * @param array $scripts script values to process.
     * @return string HTML output
     */
    protected function renderScriptBatch(array $scripts)
    {
        $html = '';
        $scriptBatches = array();
        foreach($scripts as $scriptValue)
        {
            if(is_array($scriptValue))
            {
                $scriptContent = $scriptValue['content'];
                unset($scriptValue['content']);
                $scriptHtmlOptions = $scriptValue;
            }
            else
            {
                $scriptContent = $scriptValue;
                $scriptHtmlOptions = array();
            }
            $key=serialize(ksort($scriptHtmlOptions));
            $scriptBatches[$key]['htmlOptions']=$scriptHtmlOptions;
            $scriptBatches[$key]['scripts'][]=$scriptContent;
        }
        foreach($scriptBatches as $scriptBatch)
            if(!empty($scriptBatch['scripts']))
                $html.=HtmlHelper::script(implode("\n",$scriptBatch['scripts']),$scriptBatch['htmlOptions'])."\n";

        return $html;
    }

    /**
     * Inserts the scripts at the beginning of the body section.
     *
     * @param \smarty_care $smarty
     */
    protected function renderBodyBegin($smarty)
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        $html='';
        if(isset($instance->scriptFiles[self::POS_BEGIN]))
        {
            foreach($instance->scriptFiles[self::POS_BEGIN] as $scriptFileUrl=>$scriptFileValue)
            {
                if(is_array($scriptFileValue))
                    $html.=HtmlHelper::scriptFile($scriptFileUrl,$scriptFileValue)."\n";
                else
                    $html.=HtmlHelper::scriptFile($scriptFileUrl)."\n";
            }
        }
        if(isset($instance->scripts[self::POS_BEGIN]))
            $html.=$instance->renderScriptBatch($this->scripts[self::POS_BEGIN]);

        if($html!=='')
        {
            $smarty->assign('ClientScriptBodyBegin', $html);
        }
    }


    /**
     * Inserts the scripts at the end of the body section.
     *
     * @param \smarty_care $smarty
     */
    protected function renderBodyEnd($smarty)
    {
        $instance = self::getInstance();
        /** @var ClientScript $instance */
        if(!isset($instance->scriptFiles[self::POS_END]) && !isset($instance->scripts[self::POS_END])
            && !isset($instance->scripts[self::POS_READY]) && !isset($instance->scripts[self::POS_LOAD]))
            return;

        // Always full page?
        $fullPage=1;
        $html='';
        if(isset($instance->scriptFiles[self::POS_END]))
        {
            foreach($instance->scriptFiles[self::POS_END] as $scriptFileUrl=>$scriptFileValue)
            {
                if(is_array($scriptFileValue))
                    $html.=HtmlHelper::scriptFile($scriptFileUrl,$scriptFileValue)."\n";
                else
                    $html.=HtmlHelper::scriptFile($scriptFileUrl)."\n";
            }
        }
        $scripts=isset($instance->scripts[self::POS_END]) ? $instance->scripts[self::POS_END] : array();
        if(isset($instance->scripts[self::POS_READY]))
        {
            if($fullPage)
                $scripts[]="jQuery(function($) {\n".implode("\n",$instance->scripts[self::POS_READY])."\n});";
            else
                $scripts[]=implode("\n",$instance->scripts[self::POS_READY]);
        }
        if(isset($instance->scripts[self::POS_LOAD]))
        {
            if($fullPage)
                $scripts[]="jQuery(window).on('load',function() {\n".implode("\n",$instance->scripts[self::POS_LOAD])."\n});";
            else
                $scripts[]=implode("\n",$instance->scripts[self::POS_LOAD]);
        }
        if(!empty($scripts))
            $html.=$instance->renderScriptBatch($scripts);

        if ($html) {
            $smarty->assign('ClientScriptBodyEnd', $html);
        }
    }
}