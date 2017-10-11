<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.form.form' );
?>
<div class="jCallBack-content">
	<div class="form_item">
		<form action="<?php echo JRoute::_( '', true, $params->get('usesecure')); ?>" method="post" id="jCallbackForm" name="feedback_form" onsubmit="return jCallback_send(this)">
			<input type="hidden" name="form_send" value="1" />
			
            <?php if(!empty($fields)): ?>
            <div class="controls-group">
            <?php foreach($fields as $sid=>$field): ?>
                <div class="control-group">
                <?php switch($field->model_field): 
                    case 'field':?>
                        <?php if(isset($field->show_label_field) && $field->type_field != 'hidden'): ?>
                        <div class="control-label"><label for="for_input_<?=$module->id?>_gen_<?=$sid?>">
                        <?=$field->label_field?>
                        <?php if(isset($field->required_field)): ?>
                        <span style="color: #ff0000; font-size: 12px; vertical-align: top;">*</span>
                        <?php endif; ?>
                        </label></div>
                        <?php endif; ?>
                        
                        <div id="input_<?=$module->id?>_gen_<?=$sid?>" class="field_input control-input input-<?=$field->type_field?> <?=$field->class_field?>">
                            <?php
                            switch($field->type_field){
                                case 'text':
                                case 'email':
                                case 'number':
                                case 'range':
                                case 'tel':
                                case 'password':
                                case 'file':
                                    echo '<input id="for_input_'.$module->id.'_gen_'.$sid.'" type="'.$field->type_field . 
                                        '" name="fsid_'.$sid.'" value="'.$field->default_field . 
                                        '"'.
                                    (isset($field->show_label_on_field) ? ' placeholder="'.$field->label_field.'"' : '') . 
                                    (isset($field->required_field) ? ' required="required"' : '') . 
                                    (isset($field->readonly_field) ? ' readonly="readonly"' : '') . 
                                    (isset($field->disabled_field) ? ' disabled="disabled"' : '') . 
                                    (isset($field->onchange_field) && $field->onchange_field ? ' onchange="'.$field->onchange_field.'"' : '')
                                    .'/>';
                                    break;
                                case 'textarea':
                                    echo '<textarea id="for_input_'.$module->id.'_gen_'.$sid.'" name="fsid_'.$sid.'" cols="'.$field->cols_field.'" rows="'.$field->rows_field.'"'.
                                    (isset($field->show_label_on_field) ? ' placeholder="'.$field->label_field.'"' : '') . 
                                    (isset($field->required_field) ? ' required="required"' : '') . 
                                    (isset($field->readonly_field) ? ' readonly="readonly"' : '') . 
                                    (isset($field->disabled_field) ? ' disabled="disabled"' : '') . 
                                    (isset($field->onchange_field) && $field->onchange_field ? ' onchange="'.$field->onchange_field.'"' : '') 
                                    .' >'.$field->default_field.'</textarea>';
                                    break;
                                case 'select':
                                    echo '<select id="for_input_'.$module->id.'_gen_'.$sid.'" name="fsid_'.$sid.'" class="select_cb" '.
                                    (isset($field->required_field) ? ' required="required"' : '') . 
                                    (isset($field->multiple_field) ? ' multiple="multiple"' : '') . 
                                    (isset($field->disabled_field) ? ' disabled="disabled"' : '') . 
                                    (isset($field->onchange_field) && $field->onchange_field ? ' onchange="'.$field->onchange_field.'"' : '')
                                    .'>';
                                    if(isset($field->select_option) && $field->select_option){
                                        foreach($field->select_option as $opt){
                                            echo '<option value="'.$opt->name.'">'.$opt->name.'</option>';
                                        }
                                    }       
                                    echo '</select>';
                                    break;
                                case 'checkbox':
                                case 'radio':
                                    if(isset($field->select_option) && $field->select_option){
                                        echo '<div id="for_input_'.$module->id.'_gen_'.$sid.'">';
                                        foreach($field->select_option as $opt){
                                            echo '<label><input type="'.$field->type_field.'" name="'.($field->type_field=='checkbox' ? 'fsid_'.$sid.'[]' : 'fsid_'.$sid).'" value="'.$opt->name.'" '.
                                            (isset($field->required_field) ? ' required="required"' : '') . 
                                            (isset($field->onchange_field) && $field->onchange_field ? ' onchange="'.$field->onchange_field.'"' : '')
                                            .' /></label>';
                                        }
                                        echo '</div>';
                                    }
                                    break;
                                case 'hidden':
                                    echo '<input type="'.$field->type_field.'" name="fsid_'.$sid.'" value="'.$field->default_field.'" />';
                                    break;
                                default:
                                
                                    break;
                            }
                            ?>
                        </div>
                        
                        <?php break; ?>
              <?php case 'captcha': ?>
                        <?php if ($show_kcaptcha): ?>
                        <?php if($type_captcha == 0): ?>
                            <div class="field_kcaptcha input_kcaptcha">
            					<div class="field_error" style="height: 31px; line-height: 31px"><?php echo JText::_('modcallback_enter_kcaptcha') ?></div>
            					<span><?php echo JText::_('modcallback_enter_kcaptcha') ?></span>
            					<div><img src="<?php echo $path_kcaptcha;?>image.php" /></div>
            					<input type="text" id="kcaptcha_code" name="kcaptcha_code" />
            				</div>
                        <?php elseif($type_captcha == 1): ?>
                            <input type="hidden" name="qCaptcha" />
            				<div class="field_qcaptcha input_qcaptcha">
            					<div id="jCallback_QapTcha" class="jCallback_QapTcha"></div>
            				</div>
                        <?php elseif($type_captcha == 2): ?>
                            <?php
                            $form   = JForm::getInstance('form',JPATH_BASE.'/modules/mod_callback/elements/forms/form.xml');
                            echo $form->getInput('captcha');
                            ?>
                        <?php endif; ?>
                        
                        <?php endif; ?>
                        <?php break; ?>
              <?php case 'text': ?>
                        <?php if(isset($field->show_label_field)): ?>
                        <h3><?=$field->label_field?></h3>
                        <?php endif; ?>
                        <?php if($field->desc_field): ?>
                        <p class="field_desc"><?=$field->desc_field?></p>
                        <?php endif; ?>
                        <?php break; ?>
              <?php case 'button': ?>
                        <div class="field_submit control-button">
                            <input class="<?=$field->class_field?>" type="<?=$field->type_field?>" name="<?=$field->type_field?>" value="<?=$field->label_field?>" />
                        </div>
                        <?php break; ?>
              <?php default: ?>
                        <div class="clr"></div>
                        <?php break; ?>
                <?php endswitch; ?>
                </div>
                <div class="clr"></div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
			<input type="hidden" name="module_id" value="<?php echo $module->id;?>" />
            
			<input type="hidden" name="title_cb" value="<?php echo addslashes($doc->getTitle());?>" />
			<input type="hidden" name="referal" value="<?php echo $return; ?>" />
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid') ?>" />
		</form>
	</div>
</div>