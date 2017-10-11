<?php
/**
 * A.Kernel Callback! Module Callback
 * 
 * @package    Joomla 1.5/1.6/1.7/2.5/3.0/3.6
 * @license    http://www.akernel.ru/license/callback.html
 * @copyright  Copyright (C) 2017. All rights reserved.
 * 
 * @Module     Callback aKernel
 * @copyright  Copyright (C) A.Kernel www.akernel.ru
 */
defined('_JEXEC') or die;
jimport( 'joomla.form.form' );
use Joomla\Registry\Registry;

class modCallbackHelper
{
	private function encrypt($encrypt, $type = 'module', $base64 = true, $crypt_alg = '') {
		define ("CRYPT_ALG", MCRYPT_RIJNDAEL_256); // Алгоритм по умолчанию
		define ("CRYPT_KEY", md5(md5('denvekernel')));
		
	    $crypt_alg = $crypt_alg ? $crypt_alg : CRYPT_ALG;
	    $iv = mcrypt_create_iv(mcrypt_get_iv_size($crypt_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);
	    $passcrypt = mcrypt_encrypt($crypt_alg, CRYPT_KEY, $encrypt, MCRYPT_MODE_ECB, $iv);
	    $hash = $base64 ? base64_encode($passcrypt) : $passcrypt;
	    
		$hash = md5($hash.md5(md5('kernedenver'.'callback2')));
	    return $hash;
	}
    // проверка лицензии
	function checkDomain($params) {
        if(JRequest::getVar('callback-license')==1){
           return true;
        }
		$domain = preg_replace('/^www\./si', '', $_SERVER['SERVER_NAME']);
		$arr_domain = explode('.', $domain);
		if (count($arr_domain) < 2) {
			return false;
		}
		$license = trim($params->get('license_module'));
		if ($license == $this->encrypt($domain)){
            JRequest::setVar('callback-license',1);
			return true;
		} else
            return false;
	}
    // адрес текущей страницы
    public static function getReturnUrl()
	{
		$url = JUri::getInstance()->toString();

		return base64_encode($url);
	}
    // запуск параметров модуля
    public function instance($module,$params){
        $session = JFactory::getSession();
        if($params->get('show_auto') != 0 && $params->get('mod_type')!='content'){
            $this->callBackAuto($module->id,$params->get('show_limit', 10));
        } elseif($session->has('callback.auto')) $session->clear('callback.auto');
        
        $this->loadCallBackMedia(
                $params,
                $module->id );
    }

    public function callBackAuto($module_id,$limit){
        $session = JFactory::getSession();
        
    	if($limit > 0) {
            $callbackAuto = $session->get('callback.auto',false);
            if($callbackAuto){
                if($callbackAuto['date'] == date('d-m-Y')){
                    if($callbackAuto['num'] <= $limit && $callbackAuto['module_id'] == $module_id){
                        $callbackAuto['num']++;
                        $session->set( 'callback.auto', $callbackAuto );
                    }
                }else{
                    $callbackAuto = array('date' => date('d-m-Y'), 'num' => 1, 'module_id' => $module_id);
                    $session->set( 'callback.auto', $callbackAuto );
                }
            }else{
                $callbackAuto = array('date' => date('d-m-Y'), 'num' => 1, 'module_id' => $module_id);
                $session->set( 'callback.auto', $callbackAuto );
            }
    	} elseif($session->has('callback.auto')) $session->clear('callback.auto');
    }
    
	public function loadCallBackMedia($params,$module_id){
        $doc = JFactory::getDocument();    
        
        //$doc->addScript('https://www.google.com/recaptcha/api.js');
        
        if ($params->get('use_jquery', 1) && (int)JVERSION != 3) {
            JHtml::script('mod_callback/jquery.min.js',false,true);
        }
        
        if($params->get('show_captcha', 1) && $params->get('type_captcha', 0) == 1) {
            JHtml::script('mod_callback/jquery.QapTcha.js',false,true);
            JHtml::script('mod_callback/jquery-ui.js',false,true);
            JHtml::script('mod_callback/jquery.ui.touch.js',false,true);
        }
        
        JHtml::script('mod_callback/jquery.easing.js',false,true);
        JHtml::script('mod_callback/jquery.mask.js',false,true);
        JHtml::script('mod_callback/jquery.formStyler.js',false,true);
        
        
        JHtml::stylesheet('mod_callback/animate.min.css',false,true);
        
        $style_file = $params->get('tmpl', 'default').'/style.css';
        $template = JFactory::getApplication()->getTemplate();
        $path = JPATH_THEMES . "/$template/html/mod_callback/$style_file";
        if(file_exists($path)){
            JHtml::stylesheet('templates/'.$template.'/html/mod_callback/'.$style_file);
        }else{
            JHtml::stylesheet('modules/mod_callback/tmpl/'.$style_file);
        }
        
        if($params->get('show_open')==2){
            $doc->addStyleSheet(JURI::root(true).'/modules/mod_callback/libs/fancybox/jquery.fancybox.min.css');
            $doc->addScript(JURI::root(true).'/modules/mod_callback/libs/fancybox/jquery.mousewheel-3.0.6.min.js');
            $doc->addScript(JURI::root(true).'/modules/mod_callback/libs/fancybox/jquery.fancybox.min.js');
            $doc->addScript(JURI::root(true).'/modules/mod_callback/libs/fancybox/script.js');
        }elseif($params->get('show_open')==3){
            $doc->addStyleSheet(JURI::root(true).'/modules/mod_callback/libs/fancybox-3/jquery.fancybox.min.css');
            $doc->addScript(JURI::root(true).'/modules/mod_callback/libs/fancybox-3/jquery.fancybox.min.js');
            $doc->addScript(JURI::root(true).'/modules/mod_callback/libs/fancybox-3/script.js');
        }
        
        $js_captcha1 = '';
        if($params->get('show_captcha', 1) && $params->get('type_captcha', 0) == 1 && !JRequest::getVar('callback-media-captcha1')){
            $js_captcha1 = "
                jQuery( document ).ready(function() {			
            		jQuery('.jCallback_QapTcha').QapTcha({
            			disabledSubmit:false,
            			autoRevert:true,
            			autoSubmit:false,
            			txtLock:'".JText::_('MOD_CALLBACK_ENTER_QCAPTCHA')."',
            			txtUnlock:'',
            			PHPfile : '/modules/mod_callback/libs/qcaptcha.php'			
            		});
            	});
            ";
            JRequest::setVar('callback-media-captcha1',1);
        }
        
        $js_mask = '';
        $fields = array_values($params->get('fields',array()));
        if(!empty($fields)){
            foreach($fields as $sid=>$field){
                if($field->model_field=='field' && $field->type_field=='text'){
                    $js_mask .= $field->mask_field ? 'jQuery("#for_input_'.$module_id.'_gen_'.$sid.'").mask("'.$field->mask_field.'");' : '';
                }
            }
        }
        
        $js_media = "jQuery('.jCallBack-content select').styler();";
        if($params->get('show_open')==1 && $params->get('mod_type','popup')=='popup'){
            $js_media .= "
                var acb_height,acb_width,top_block,left_block;
                var window_h = jQuery(window).height(), window_w = jQuery(window).width();
                
                
            	jQuery('#aKernelcallback-".$module->id.":not(.acallback-OnDown)').each(function(){
                    var el = jQuery(this);
                    el.css({
                        'visibility': 'hidden',
                        'display': 'block'
                    });
                    acb_width = el.width();
                    acb_height = el.height();
                    el.css({
                        'visibility': 'visible',
                        'display': 'none'
                    });
                    if(acb_height < window_h){
                        top_block = (window_h-acb_height)/2;
                    }else{
                        acb_height = window_h-20;
                        top_block = 10;
                        el.css({
                            height: acb_height,
                            'overflow-y': 'scroll'
                        });
                    }
                    left_block = acb_width < window_w ? (window_w-acb_width)/2 : 0;
                    el.css({
                        top: top_block,
                        left: left_block
                    });
                });
            ";
        }
        if($params->get('show_open')==1 && !JRequest::getVar('callback-media') && $params->get('mod_type','popup')=='popup'){
            $js_media.="
            	jQuery('.aKernelcallback-fade, .acallback_close').click(function(){
                    var el = jQuery('.jCallBack-popup:visible');
                    if(el.length>0){
                        var animated = el.data('animation');
                        var animatedIn = ['In','Down','Left','Right','Up'];
                        var animatedOut = ['Out','Up','Right','Left','Down'];
                        var new_animated = (function(){
                            var a = animated;
                            for(var i=0; i <  animatedIn.length; i++){
                                a=a.replace(animatedIn[i],animatedOut[i]);
                            }
                            return a;
                        }).call();
                        if(animated!='jQ'){
                            jQuery('.aKernelcallback-fade').fadeOut(900);
                            el.removeClass(animated).addClass(new_animated);
                            setTimeout(function(){
                                el.hide();
                            },900);
                        }else{
                            el.fadeOut(el.data('animationspeed'), function(){
                        		jQuery('.aKernelcallback-fade').fadeOut((el.data('animationspeed')-200));
                        	});
                        }
                    }else{
                        jQuery('.aKernelcallback-fade').fadeOut(300);
                        jQuery('.aCallBackResult').fadeOut(300);
                    }
                });
                
                jQuery('.show_callback').click(function(){
                    var el = jQuery(this);
                    var animated = el.data('animation');
                    var animatedIn = ['In','Down','Left','Right','Up'];
                    var animatedOut = ['Out','Up','Right','Left','Down'];
                    var new_animated = (function(){
                        var a = animated;
                        for(var i=0; i <  animatedIn.length; i++){
                            a=a.replace(animatedIn[i],animatedOut[i]);
                        }
                        return a;
                    }).call();
                    if(animated!='jQ'){
                        jQuery('#jCallBack_fade').fadeIn();
                        jQuery('#jCallBack-'+el.data('moduleid')).show();
                        jQuery('#jCallBack-'+el.data('moduleid')).removeClass(new_animated).addClass(animated);
                    }else{
                        jQuery('#jCallBack_fade').fadeIn((el.data('animationspeed')-200), function(){
                    		jQuery('#jCallBack-'+el.data('moduleid')).fadeIn(el.data('animationspeed'));
                    	});
                    }
                });";
                JRequest::setVar('callback-media',1);
        }
        
        $js_auto='';
        $session = JFactory::getSession();
        $callbackAuto = $session->get('callback.auto',false);
        if($params->get('mod_type')=='popup' && $params->get('show_auto')==1 && $callbackAuto && $callbackAuto['num'] <= $params->get('show_limit') && !JRequest::getVar('callback-media-auto')){
            $js_auto.="
                setTimeout(function() {
            		if(!jQuery('#jCallBack-".$module_id."').is(':visible')) jQuery('.show_callback[data-moduleid=\"".$module_id."\"]').click();
            	}, ".($params->get('animation_speed')*1000).");";
            JRequest::setVar('callback-media-auto',1);
        }
        
        $js="
        jQuery(function(){
        ".$js_captcha1 .
        $js_mask .
        $js_media .
        $js_auto."
        });
        function aCallback_send(form) {
            jQuery.post('/index.php?option=com_ajax&module=callback&format=json&tmpl=component', jQuery(form).serialize(), function(json, textStatus){
                var result,fade,open_form;
                if(jQuery('.aKernelcallback-popup:visible').length>0){
                    open_form = jQuery('.aKernelcallback-popup:visible').children('.aKernelcallback-form');
                }
                if(jQuery.isPlainObject(json.messages)){
                    var msg = '';
                    jQuery.each(json.messages, function(i, v){
                        if(i=='notice' || i=='warning'){
                            jQuery.each(v, function(ii, vv){
                                msg += vv+'<br />';
                            });
                        }
                    });
                    if(msg.length>0){
                        result = jQuery('<div />').addClass('aCallBackResult').css({'display':'none'});
                        result.append(jQuery('<div />').html(msg));
                        if(open_form){
                            result.appendTo(open_form);
                        }else{
                            result.appendTo(jQuery('body'));
                        }
                        result.fadeIn();
                        setTimeout(function(){
                            result.fadeOut(300,function(){result.remove();});
                        },5000);
                        return;
                    }
                }
                if(json.success){
                    result = jQuery('<div />').addClass('aCallBackResult').css({'display':'none'});
                    result.append(jQuery('<div />').html(json.message));
                    result.appendTo(jQuery('body'));
                    jQuery.fancybox.close();
                    form.reset();
                    result.fadeIn(300);
                    setTimeout(function(){
                        result.fadeOut(300,function(){result.remove();});
                    },5000);
                }else{
                    result = jQuery('<div />').addClass('aCallBackResult').css({'display':'none'});
                    result.append(jQuery('<div />').html(json.message));
                    if(open_form){
                        result.appendTo(open_form);
                    }else{
                        result.appendTo(jQuery('body'));
                    }
                    result.fadeIn(300);
                    setTimeout(function(){
                        result.fadeOut(300,function(){result.remove();});
                    },5000);
                    return;
                }
            },'json');
        }";
        
        $doc->addScriptDeclaration($js);
        
        if(!JRequest::getVar('callback-media-css')){
            $doc->addStyleDeclaration("
                .jCallbackBttn,
                .jCallbackBttnOnDown{
                    border-color: ".$params->get('bd_button').";
                    color: ".$params->get('fg_button').";
                    background-color: ".$params->get('bg_button').";
                }
                .jCallbackBttn:hover,
                .jCallbackBttnOnDown:hover{
                    border-color: ".$params->get('bd_hover_button').";
                    color: ".$params->get('fg_hover_button').";
                    background-color: ".$params->get('bg_hover_button').";
                }
            ");
            JRequest::setVar('callback-media-css',1);
        }
	}
    
    public static function getAjax() {
        $app = JFactory::getApplication();
		$jinput  = $app->input;  
        $post = $jinput->post;
        $module = JTable::getInstance('Module', 'JTable', array());
		$module->load($jinput->get('module_id'));
        
        $registry = new Registry;
        $registry->loadString($module->params);
        $params = $registry;
        
        $fields = array_values($params->get('fields',array()));
        $show_kcaptcha = $params->get('show_kcaptcha', 1);
        $type_captcha = $params->get('type_captcha', 0);
        
        $error = false;
        $message = '';
        $data = array();
                
        if($show_kcaptcha){
            switch($type_captcha){
                case 0: 
                    if($post->getVar('kcaptcha_code','')!=$_SESSION['callback-captcha-code']){
                        $message.= JText::_('MOD_CALLBACK_INVALID_KCAPTCHA');
                        $error=true;
                    }
                    break;
                case 1:
                    if($post->getVar('qCaptcha','')!=1){
                        $message.= JText::_('MOD_CALLBACK_INVALID_QCAPTCHA');
                        $error=true;
                    }
                    break;
                case 2:
                    $form   = JForm::getInstance('callbackform',JPATH_BASE.'/modules/mod_callback/forms/form.xml');
                    if(!$form->validate(JRequest::get('post'))){
                        $message.= JText::_('MOD_CALLBACK_INVALID_JCAPTCHA');
                        $error=true;
                    }
                    break;
            }
        }
        $result = self::validate($fields,$post);
        $message .= $result['message'] ? '<br />'.$result['message'] : '';
        $error = $error ? $error : $result['error'];
        
        if($error){
            exit(new JResponseJson($data,$message,$error));
        }
        
        if(self::SendCallback($fields,$post,$params,$module->title)){
            $message = JText::_($params->get('text_success'));
            $error = false;
        }else{
            $message = JText::_('modcallback_send_error');
            $error = true;
        }
        exit(new JResponseJson($data,$message,$error));
	}
    
    private static function validate($fields,$post){
        if($fields){
            $message = '';
            $error=false;
            foreach($fields as $sid=>$field){
                if(isset($field->required_field)){
                    if($field->type_field=='email'){
                        if(!(bool)preg_match(chr(1) . '^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$' . chr(1), $post->getVar('fsid_'.$sid,false))){
                            $message .= JText::sprintf('MOD_CALLBACK_FIELD_ERROR', $field->label_field).'<br />';
                            $error = true;
                        }
                    }else{
                        if(!$post->getVar('fsid_'.$sid,false)){
                            $message .= JText::sprintf('MOD_CALLBACK_FIELD_ERROR', $field->label_field).'<br />';
                            $error = true;
                        }
                    }
                }
            }
            return array('message'=>$message,'error'=>$error);
        }
        return array('message'=>'','error'=>false);
    }
    
    
    /**
     * Письмо на e-mail с информацией о просящем перезвонить.
     */
    private static function SendCallback( $fields, $post, $params, $module_title )
    {
        $app = JFactory::getApplication();
        
        $fromname = $app->get('fromname',$module_title);
		$sitename = $app->get('sitename');
        $sitemail = $app->get('mailfrom');
        
        jimport('joomla.mail.mail');
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        
        
        if($params->get( 'send_mail_type',1)==1){
            $mailer->addRecipient($config->get( 'mailfrom' ));
        }else{
            $sender = array( 
                $config->get( 'mailfrom' ),
                $config->get( 'fromname' ) 
            );
            $mailer->setSender($sender);
            $mailer->addRecipient(array($params->get( 'send_mail')));
        }
        
        $title = '"'.stripslashes($post->getVar('title_cb')).'"';
        
		$referal = $post->getVar('referal',false) ? base64_decode($post->getVar('referal',false)) : '';

        $this_date = date('d-m-Y H:i:s');
        $title = $sitename . ' | ' . $module_title . ' | ' . $params->get( 'subject_email');
        
        $msg = '<div></div>';
        
        $msg .= '<table><tbody><tr><td></td></tr></tbody></table>';
        $msg .= '<table>';
        $msg .= '<thead>';
        $msg .= '<tr>';
        $msg .= '<td>'.$title.'</td>';
        $msg .= '</tr>';
        $msg .= '<tr>';
        $msg .= '<td>Отправлено: '.$this_date.'</td>';
        $msg .= '</tr>';
        $msg .= '</thead>';
        $msg .= '<tbody>';
        $msg .= '<tr>';
        $msg .= '<td>';
        $msg .= '<table>';
        $msg .= '<tbody>';

        foreach($fields as $sid => $field){
            if($field->model_field=='field'){
                if(in_array($field->type_field,array('text','textarea','radio','email','number','tel','password','hidden'))){
                    if($post->getVar('fsid_'.$sid,false)){
                      $msg .= '<tr>';
                      $msg .= '<td>'.$field->label_field.': </td>';
                      $msg .= '<td>'.$post->getVar('fsid_'.$sid,false).'</td>';
                      $msg .= '</tr>';
                    } 
                }elseif(in_array($field->type_field,array('checkbox'))){
                    if($post->getVar('fsid_'.$sid,false)){
                        foreach($post->getVar('fsid_'.$sid) as $val){
                            $msg .= '<tr>';
                              $msg .= '<td>'.$val.': </td>';
                              $msg .= '<td>'.JText::_('MOD_CALLBACK_YES').'</td>';
                              $msg .= '</tr>';
                        }
                    }
                }elseif(in_array($field->type_field,array('select'))){
                    if(isset($field->multiple_field)){
                        if($post->getVar('fsid_'.$sid,false)){
                            foreach($post->getVar('fsid_'.$sid) as $val){
                                $msg .= '<tr>';
                              $msg .= '<td>'.$field->label_field.': </td>';
                              $msg .= '<td>'.$val.'</td>';
                              $msg .= '</tr>';
                            }
                        }
                    }else{
                        if($post->getVar('fsid_'.$sid,false)){
                            $msg .= '<tr>';
                              $msg .= '<td>'.$field->label_field.': </td>';
                              $msg .= '<td>'.$post->getVar('fsid_'.$sid,false).'</td>';
                              $msg .= '</tr>';
                        }
                    }
                }
            }
        }      
		if(!$referal) $referal = JText::_('MOD_CALLBACK_UNKNOWN');
        $msg .= '</tbody>';
        $msg .= '</table>';
        $msg .= '</td>';
        $msg .= '</tr>';
        $msg .= '</tbody>';
        $msg .= '<tfoot>';
        $msg .= '<tr>';
        $msg .= '<td>'.JText::_('MOD_CALLBACK_REFERAL_URL').' '.$referal.'</td>';
        $msg .= '</tr>';
        $msg .= '</tfoot>';
        $msg .= '</table>';
        $msg .= '<div></div>';
        $msg .= '<br />';
        $msg .= '<div>'.JText::_('MOD_CALLBACK').'</div>';
        $msg .= '<br />';
        $msg .= '<div>'.JText::_('MOD_CALLBACK_DESC').'</div>';
        
        $subject = $title;
        
        $body = "\r\n".$msg."\r\n";
        
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $result = $mailer->Send();
		
		$phones = explode(',', $params->get('sms_phones'));
		
       	if($params->get('sms_status') == 1) {

			if(count($phones) == 1) {
				$ch = curl_init("http://sms.ru/sms/send");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array(

					"api_id"		=>	$params->get('sms_login'),
					"to"			=>	$params->get('sms_phones'),
					"text"		=>	$body

				));
				$body = curl_exec($ch);
				curl_close($ch);   				
			} else {
				foreach ($phones as $val) {
					$ch = curl_init("http://sms.ru/sms/send");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POSTFIELDS, array(

						"api_id"		=>	$params->get('sms_login'),
						"to"			=>	$val,
						"text"		=>	$body

					));
					$body = curl_exec($ch);
					curl_close($ch);  					
				}
 
			}     	
        }

        return $result;
    }
    
}
?>