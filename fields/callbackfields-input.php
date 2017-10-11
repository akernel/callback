<?php
defined('_JEXEC') or die;
jimport ('joomla.html.html.bootstrap');
JHtml::_('jquery.ui', array('core', 'sortable'));
JHtml::_('script', 'jui/sortablelist.js', false, true);
JHtml::_('stylesheet', 'jui/sortablelist.css', false, true, false);

$params = $this->_params;
$name = $this->name;
$fields = $this->value ? array_values($this->value) : array();

JFactory::getDocument()->addScriptDeclaration(
	"
    var name = '".$name."';
    var fields_json = ".json_encode($fields).";
    var count = ".(int)count($fields).";
    var index_field = 0;
    var index_option = [];
	jQuery(document).ready(function ($){
        jQuery('#enter-field').closest('.control-group').after(\"<div id='block-forms' class='control-group'><table class='table' id='fieldsList'><tbody></tbody></table></div>\");
        jQuery.each(fields_json, function(i, v){
            inputCreate(i,v);
            index_field++;
        });
        var sortableList = new $.JSortableList('#fieldsList');
        var block_Elem = jQuery('#block-forms table tbody');
        
        jQuery('#enter-field').click(function(e){
            e.preventDefault();
            inputCreate(index_field,{});
            index_field++;
        });
        
        jQuery('body').on('change','.model-field',function(){
            var select = jQuery(this);
            select.closest('.accordion-inner').children('*:not(.model-field)').each(function(){
                jQuery(this).remove();
            });
            ChangeModelField(select,{},select.data('idfield'));
            var model;
            switch (select.val()) {
                    case 'captcha':
                        model = '".JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_CAPTCHA')."';
                        break
                    case 'button':
                        model = '".JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_BUTTON')."';
                        break
                    case 'text':
                        model = '".JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_TEXT')."';
                        break
                    case 'field':
                        model = '".JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_FIELD')."';
                        break
                    default:
                        model = '';
                        break
                }
            select.closest('tr.form-field-block').find('.model-field-heading').text(model);
            select.closest('tr.form-field-block').find('.label-field').keyup(function(){
                jQuery(this).closest('tr.form-field-block').find('.label-field-heading').text(jQuery(this).val());
            });
        });
        
        jQuery('body').on('click','.field-delete',function(e){
            e.preventDefault();
            
            if(confirm('".JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_DELETE_QUESTION')."')){
                jQuery(this).closest('tr.form-field-block').remove();
            }
        });
        
        jQuery('body').on('click','.delete-option',function(e){
            e.preventDefault();
            jQuery(this).closest('.block-option').remove();
        });
	});
	"
);

?>
<script type="text/javascript">
function inputCreate(index,params){
    var block_Elem = jQuery('#block-forms table tbody');
        
    var model = (function(){
            switch (params.model_field) {
                case 'captcha':
                    return '<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_CAPTCHA');?>';
                    break
                case 'button':
                    return '<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_BUTTON');?>';
                    break
                case 'text':
                    return '<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_TEXT');?>';
                    break
                case 'field':
                    return '<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_FIELD');?>';
                    break
                default:
                    return '';
                    break
            }
        }).call();
    
    
    block_Elem.append('\
    <tr id="field-block-'+index+'" class="form-field-block">\
        <td width="1%" class="order nowrap center"><span class="sortable-handler"><span class="icon-menu"></span></span></td>\
        <td><div id="field-groups-id-'+index+'" class="accordion"><div class="accordion-group">\
              <div class="accordion-heading"><a class="btn btn-small field-delete"><span class="icon-cancel"></span> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_DELETE'); ?></a><a href="#field_slide_'+index+'" data-toggle="collapse" class="accordion-toggle collapsed">#'+index+' <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_FIELD');?> "<span class="model-field-heading">'+model+'</span>"&nbsp;&nbsp;&nbsp;<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TITLE');?> "<span class="label-field-heading">'+params.label_field+'</span>"&nbsp;&nbsp;&nbsp;<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD');?> "<span class="type-field-heading">'+params.type_field+'</span>"</a></div>\
              <div class="accordion-body collapse" id="field_slide_'+index+'"><div class="accordion-inner">\
                    <select name="'+name+'['+index+'][model_field]" class="model-field" data-idfield="'+index+'"><option value=""> - <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD'); ?> - </option><option value="field"'+(params.model_field=='field' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_FIELD');?></option><option value="captcha"'+(params.model_field=='captcha' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_CAPTCHA');?></option><option value="button"'+(params.model_field=='button' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_BUTTON');?></option><option value="text"'+(params.model_field=='text' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_TEXT');?></option></select>\
              </div></div>\
        </div></div></td>\
    </tr>\
    ');
    
    ChangeModelField(jQuery("#field-block-"+index).find('.model-field'),params,index);
}
function ChangeModelField(select,params,index){
    var value = select.val();
    if(!value){
        return;
    }
    switch (value) {
    case 'captcha':
        CreateCaptchaField(jQuery(select).closest('.accordion-inner'),params,index);
        break
    case 'button':
        CreateButtonField(jQuery(select).closest('.accordion-inner'),params,index);
        break
    case 'text':
        CreateTextField(jQuery(select).closest('.accordion-inner'),params,index);
        break
    case 'field':
        CreateFieldsField(jQuery(select).closest('.accordion-inner'),params,index);
        break
    }
}

function CreateCaptchaField(parent,params,index){
    parent.append('\
<input type="text" name="'+name+'['+index+'][label_field]" class="label-field input-callback" value="'+(params.label_field ? params.label_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_LABEL_FIELD'); ?>" />\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][show_label_field]" value="1"'+(params.show_label_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_SHOW_LABEL_FIELD'); ?></label>\
<input type="hidden" name="'+name+'['+index+'][type_field]" value="captcha" />\
<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MODEL_FIELD_CAPTCHA_TEXT'); ?>');
parent.closest('tr.form-field-block').find('.type-field-heading').text('captcha');
}

function CreateButtonField(parent,params,index){
    parent.append('\
<input type="text" name="'+name+'['+index+'][label_field]" class="label-field input-callback" value="'+(params.label_field ? params.label_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_LABEL_BUTTON_FIELD'); ?>" />\
<input type="text" name="'+name+'['+index+'][class_field]" class="input-callback" value="'+(params.class_field ? params.class_field : '')+'" placeholder="Доп. класс" />\
<select name="'+name+'['+index+'][type_field]" class="type-field"><option value="">- <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_BUTTON_FIELD'); ?> -</option><option value="submit"'+(params.type_field=='submit' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_BUTTON_FIELD_SUBMIT'); ?></option><option value="reset"'+(params.type_field=='reset' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_BUTTON_FIELDRESET'); ?></option></select>');
parent.closest('tr.form-field-block').find('.type-field').change(function(){
    jQuery(this).closest('tr.form-field-block').find('.type-field-heading').text(jQuery(this).val());
});
}

function CreateTextField(parent,params,index){
parent.append('\<input type="text" name="'+name+'['+index+'][label_field]" class="label-field input-callback" value="'+(params.label_field ? params.label_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_LABEL_TITLE_FIELD'); ?>" />\
<textarea class="input-callback" name="'+name+'['+index+'][desc_field]">'+(params.desc_field ? params.desc_field : '')+'</textarea>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][show_label_field]" value="1"'+(params.show_label_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_SHOW_LABEL_TITLE_FIELD'); ?></label>\
<input type="hidden" name="'+name+'['+index+'][type_field]" value="text" />');
parent.closest('tr.form-field-block').find('.type-field-heading').text('text');
}

function CreateFieldsField(parent,params,index){
parent.append('\<input type="text" name="'+name+'['+index+'][label_field]" class="label-field input-callback" value="'+(params.label_field ? params.label_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_LABEL_FIELD'); ?>" />\
<select name="'+name+'['+index+'][type_field]" class="type-field input-callback">\
<option value="">- <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD'); ?> -</option>\
<option value="text"'+(params.type_field=='text' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_TEXT'); ?></option>\
<option value="textarea"'+(params.type_field=='textarea' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_TEXTAREA'); ?></option>\
<option value="select"'+(params.type_field=='select' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_SELECT'); ?></option>\
<option value="checkbox"'+(params.type_field=='checkbox' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_CHECKBOX'); ?></option>\
<option value="radio"'+(params.type_field=='radio' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_RADIO'); ?></option>\
<option value="email"'+(params.type_field=='email' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_EMALI'); ?></option>\
<option value="number"'+(params.type_field=='number' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_NUMBER'); ?></option>\
<option value="range"'+(params.type_field=='range' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_RANGE'); ?></option>\
<option value="tel"'+(params.type_field=='tel' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_TEL'); ?></option>\
<option value="password"'+(params.type_field=='password' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_PASSWORD'); ?></option>\
<option value="file"'+(params.type_field=='file' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_FILE'); ?></option>\
<option value="hidden"'+(params.type_field=='hidden' ? ' selected="selected"': '')+'><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_TYPE_FIELD_HIDDEN'); ?></option>\
</select>\
<input type="text" name="'+name+'['+index+'][class_field]" class="input-callback" value="'+(params.class_field ? params.class_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_CLASS_FIELD'); ?>" />\
<input type="text" name="'+name+'['+index+'][mask_field]" class="input-callback" value="'+(params.mask_field ? params.mask_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MASK_FIELD'); ?>" />\
<input type="text" name="'+name+'['+index+'][default_field]" class="input-callback" value="'+(params.default_field ? params.default_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_DEFAULT_FIELD'); ?>" />\
<input type="text" name="'+name+'['+index+'][onchange_field]" class="input-callback" value="'+(params.onchange_field ? params.onchange_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ONCHANGE_FIELD'); ?>" />\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][show_label_field]" value="1"'+(params.show_label_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_SHOW_LABEL_FIELD'); ?></label>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][show_label_on_field]" value="1"'+(params.show_label_on_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_SHOW_LABEL_ON_FIELD'); ?></label>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][required_field]" value="1"'+(params.required_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_REQUIRED_FIELD'); ?></label>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][readonly_field]" value="1"'+(params.readonly_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_READONLY_FIELD'); ?></label>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][disabled_field]" value="1"'+(params.disabled_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_DISABLED_FIELD'); ?></label>\
<label class="input-callback"><input type="checkbox" name="'+name+'['+index+'][multiple_field]" value="1"'+(params.multiple_field==1 ? ' checked="checked"': '')+' /> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_MULTIPLE_FIELD'); ?></label>\
');
if(parent.closest('tr.form-field-block').find('.type-field').val() == 'select' ||
            parent.closest('tr.form-field-block').find('.type-field').val() == 'checkbox' ||
            parent.closest('tr.form-field-block').find('.type-field').val() == 'radio'){
                
    parent.append('<div class="btn-wrapper select-change"><button class="btn btn-small add-option-select"><span class="icon-apply"></span> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ADD_OPTION'); ?></button></div>');
    if(params.select_option){
        index_option[index]=0;
        jQuery.each(params.select_option, function(ik, vk){
            optionCreate(parent.find('.btn-wrapper'),params,index,ik,vk)
            index_option[index]=ik;
        });
    }
}else if(parent.closest('tr.form-field-block').find('.type-field').val() == 'textarea'){
    parent.append('\
<input type="number" name="'+name+'['+index+'][cols_field]" class="input-callback textarea-change" value="'+(params.cols_field ? params.cols_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_COLS_FIELD'); ?>" />\
<input type="number" name="'+name+'['+index+'][rows_field]" class="input-callback textarea-change" value="'+(params.rows_field ? params.rows_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ROWS_FIELD'); ?>" />');
}

parent.closest('tr.form-field-block').find('.type-field').change(function(){
    jQuery(this).closest('tr.form-field-block').find('.type-field-heading').text(jQuery(this).val());
    jQuery(this).parent().find('.select-change,.textarea-change').each(function(){
        jQuery(this).remove();
    });
    if(jQuery(this).val() == 'select' ||
            jQuery(this).val() == 'checkbox' ||
            jQuery(this).val() == 'radio'){
        if(parent.find('.add-option-select').length==0){
            index_option[index] = index_option[index] ? index_option[index]+1 : 0;
            parent.append('<div class="btn-wrapper select-change"><button class="btn btn-small add-option-select"><span class="icon-apply"></span> <?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ADD_OPTION'); ?></button></div>');
            jQuery('.add-option-select').on('click',function(e){
                e.preventDefault();
                optionCreate(jQuery(this).closest('.btn-wrapper'),params,index,index_option[index],{});
                index_option[index]++;
            });
        }
    }else if(jQuery(this).val() == 'textarea'){
        if(parent.find('.textarea-change').length==0){
            parent.append('\
    <input type="number" name="'+name+'['+index+'][cols_field]" class="input-callback textarea-change" value="'+(params.cols_field ? params.cols_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_COLS_FIELD'); ?>" />\
    <input type="number" name="'+name+'['+index+'][rows_field]" class="input-callback textarea-change" value="'+(params.rows_field ? params.rows_field : '')+'" placeholder="<?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ROWS_FIELD'); ?>" />');
        }
    }
});
}
function optionCreate(parent,params,index,i_opt,p_opt){
    parent.append('\
    <div class="block-option select-change"><input class="input-callback" type="text" name="'+name+'['+index+'][select_option]['+i_opt+'][name]" value="'+(p_opt.name ? p_opt.name : '')+'" /><label class="input-callback"><input type="radio" name="'+name+'['+index+'][checked]" value="'+i_opt+'" /> <?php echo JText::_('MOD_CALLBACK_USE_DEFAULT'); ?></label><a class="delete-option btn btn-small"><span class="icon-cancel"></span></a></div>\
    ');
}
</script>
<style>
.field-delete{
    float: right;
    margin: 4px 4px 0 0;
}
.input-callback{
    display: inline-block;
    margin: 8px 5px;
}
</style>
<button id="enter-field" class="btn"><?php echo JText::_('MOD_CALLBACK_FIELDS_STRUCTURE_ADD_FIELD'); ?></button>