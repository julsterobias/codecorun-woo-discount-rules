/**
 * 
 * admin.js
 * @since 1.2.0
 * @author Codecorun
 * 
 */

jQuery(document).ready(function(){

    jQuery('#wcdr_select_rules__').change(function(){
        var rule = jQuery(this).val();
        jQuery(this).val('');
        wcdr_add_rules(rule);
        jQuery('html, body').animate({ scrollTop:  jQuery('.wcdr_rules_canvas__').offset().top - 50 }, 'slow');
    });

    jQuery('body').on('click','.wcdr_condition_remove_field',function(){
        if(confirm(wcdr_label_factory.confirm_rule)){
            jQuery(this).closest('.wcdr__rule_con').remove();
        }
        if(jQuery('.wcdr_rules_canvas__').find('.wcdr__rule_con').length == 0){
            jQuery('.wcdr_no_rules').show();
        }
        wcdr_remove_first_condition();
    });

    //on select from include products and add the selected product in the generated table
    //not working
    jQuery('body').on('change','.wcdr_product_list, .wcdr_category_list, .wcdr_role_list',function(){
        var type = jQuery(this).data('list-type');
        if(type != 'role' && type != 'include_category' && type != 'exclude_category')
            wcdr_add_el_to_list(type, this);
    });

    //remove the selected product
    jQuery('body').on('click','.wcdr_remove_list_from_list', function(){
        if(confirm(wcdr_label_factory.confirm_product)){
            jQuery(this).closest('tr').remove();
        }
    });

    //load saved rules
    wcdr_load_saved_rules();

    jQuery('body').on('click','.wcdr-add-metas-el, .wcdr-add-url_param-el', function(){
        var parent = jQuery(this).closest('table');
        var cloned = jQuery(this).closest('tr').clone(true);
        jQuery(parent).append(cloned);
    });

    jQuery('body').on('click','.wcdr-remove-metas-el, .wcdr-remove-url_param-el', function(){
       jQuery(this).closest('tr').remove();
    });

});

var wcdr_rule_canvas = '.wcdr_rules_canvas__';

function wcdr_add_rules(rule, value = null, generate_list = null){
    jQuery('.wcdr_no_rules').hide();

    switch(rule){
        case 'date':
            //add date UI
            var parent = wcdr_elementor__(
                {
                    type: 'div',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_date_rule_con wcdr__rule_con'
                        },
                        {
                            attr: 'data-rule-type',
                            value: rule
                        }
                    ]
                }
            );
            var rule_label = wcdr_elementor__(
                {
                    type: 'h4',
                    text: wcdr_label_factory.date
                }
            );

            wcdr_add_tooltip({
                parent: rule_label,
                text: wcdr_label_factory.tooltip_date
            });

            var el = wcdr_elementor__(
                {
                    type: 'input',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_date_rule_el'
                        },
                        {
                            attr: 'type',
                            value: 'date'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field[date-'+wcdr_unique_name()+']'
                        },
                        {
                            attr: 'value',
                            value: value
                        }
                    ]
                }
            );
            parent.appendChild(rule_label);
            parent.appendChild(el);
            jQuery(wcdr_rule_canvas).append(parent);
            wcdr_create_conditions(parent);
            break;
        case 'date-range':
            
            var field_id = wcdr_unique_name();
            var parent = wcdr_elementor__(
                {
                    type: 'div',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_date_rule_con wcdr__rule_con'
                        },
                        {
                            attr: 'data-rule-type',
                            value: rule
                        }
                    ]
                }
            );
            var rule_label = wcdr_elementor__(
                {
                    type: 'h4',
                    text: wcdr_label_factory.date_range
                }
            );

            wcdr_add_tooltip({
                parent: rule_label,
                text: wcdr_label_factory.tooltip_date_range
            });

            var label_from = wcdr_elementor__(
                {
                    type: 'label',
                    text: wcdr_label_factory.from
                }
            );
            var el_from = wcdr_elementor__(
                {
                    type: 'input',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_date_rule_el'
                        },
                        {
                            attr: 'type',
                            value: 'date'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field[date_range-'+field_id+'][from]'
                        },
                        {
                            attr: 'value',
                            value: (value)? value[0] : value
                        }
                        
                    ]
                }
            );

            var label_to = wcdr_elementor__(
                {
                    type: 'label',
                    text: wcdr_label_factory.to
                }
            );
            var el_to = wcdr_elementor__(
                {
                    type: 'input',
                    attributes: [
                        {
                            attr: 'data-rule-type',
                            value: rule
                        },
                        {
                            attr: 'class',
                            value: 'wcdr_date_rule_el'
                        },
                        {
                            attr: 'type',
                            value: 'date'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field[date_range-'+field_id+'][to]'
                        },
                        {
                            attr: 'value',
                            value: (value)? value[1] : value
                        }
                        
                    ]
                }
            );
            label_from.appendChild(el_from)
            label_to.appendChild(el_to)
            parent.appendChild(rule_label);
            parent.appendChild(label_from);
            parent.appendChild(label_to);
            jQuery(wcdr_rule_canvas).append(parent);
            wcdr_create_conditions(parent);

            break;
        case 'include':            
            var inc_parent = wcdr_create_select_list(
                {
                    type: 'include',
                    rule: rule
                }
            );
            wcdr_create_conditions(parent);
            if(generate_list){
                for(var y in generate_list){
                    wcdr_generate_added_el_to_list(inc_parent, 'include', generate_list[y].product_id, generate_list[y].product_text);
                }
            }
            break;
        case 'exclude':
            var exc_parent = wcdr_create_select_list(
                {
                    type: 'exclude',
                    rule: rule
                }
            );
            wcdr_create_conditions(parent);
            if(generate_list){
                for(var y in generate_list){
                    wcdr_generate_added_el_to_list(exc_parent, 'exclude', generate_list[y].product_id, generate_list[y].product_text);
                }
            }
            break;
        case 'role':
        case 'had_purchased_product':
        case 'include_category':
        case 'exclude_category':
                var exc_parent = wcdr_create_select_list(
                    {
                        type: rule,
                        rule: rule,
                        value: value
                    }
                );
                wcdr_create_conditions(parent);
                if(generate_list){
                    for(var y in generate_list){
                        wcdr_generate_added_el_to_list(exc_parent, rule, generate_list[y].product_id, generate_list[y].product_text);
                    }
                } 
            break;
        
        case 'count':
        case 'amount':
        case 'previous_orders':

            var label_text = '';
            var tooltip = null;

            if(rule == 'count'){
                label_text = wcdr_label_factory.items_in_cart;
                tooltip = wcdr_label_factory.tooltip_number_items;
            }else if(rule == 'amount'){
                label_text = wcdr_label_factory.total_amount;
                tooltip = wcdr_label_factory.tooltip_total_amount;
            }else if(rule == 'previous_orders'){
                label_text = wcdr_label_factory.previous_orders;
                tooltip = wcdr_label_factory.tooltip_had_prev_orders;
            }

            var a_value = [];
            for(var v in value){
                a_value.push(value[v]);
            }

            var amount_uid = wcdr_unique_name();
            var parent = wcdr_elementor__(
                {
                    type: 'div',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_'+rule+'_rule_con wcdr__rule_con'
                        },
                        {
                            attr: 'data-rule-type',
                            value: rule
                        }
                    ]
                }
            );

            var el_label = wcdr_elementor__(
                {
                    type: 'label',
                    text: label_text
                }
            );

            if(tooltip){
                wcdr_add_tooltip({
                    parent: el_label,
                    text: tooltip
                });    
            }
            
            var el_num_condition = wcdr_elementor__(
                {
                    type: 'select',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_number_condition'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field['+rule+'-'+amount_uid+'][condition]'
                        }
                    ],
                    options: [
                        {
                            text: wcdr_label_factory.less_than_equal,
                            value: 'less_than_equal'
                        },
                        {
                            text: wcdr_label_factory.greater_than_equal,
                            value: 'greater_than_equal'
                        },
                        {
                            text: wcdr_label_factory.equal,
                            value: 'equal'
                        }
                    ],
                    value: (a_value.length > 0)? a_value[0] : value
                }
            );

            var el_count = wcdr_elementor__(
                {
                    type: 'input',
                    attributes: [
                        {
                            attr: 'type',
                            value: 'number'
                        },
                        {
                            attr: 'class',
                            value: 'wcdr_'+rule+'_rule_el wcdr_number_el'
                        },
                        {
                            attr: 'placeholder',
                            value: '0'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field['+rule+'-'+amount_uid+'][value]'
                        },
                        {
                            attr: 'value',
                            value: (a_value)? a_value[1] : value
                        }
                    ]
                }
            );
            el_label.appendChild(el_num_condition);
            el_label.appendChild(el_count);
            parent.appendChild(el_label);
            jQuery(wcdr_rule_canvas).append(parent);
            wcdr_create_conditions(parent);
            break;
        
        case 'url_param':
        case 'metas':

            var label_text = (rule == 'metas')? wcdr_label_factory.meta_label : wcdr_label_factory.param_label;
            var input_key = (rule == 'metas')? wcdr_label_factory.meta_key : wcdr_label_factory.param_key;
            var input_value = (rule == 'metas')? wcdr_label_factory.meta_value : wcdr_label_factory.param_value;

            var amount_uid = wcdr_unique_name();
            var parent = wcdr_elementor__(
                {
                    type: 'div',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_'+rule+'_rule_con wcdr__rule_con'
                        },
                        {
                            attr: 'data-rule-type',
                            value: rule
                        }
                    ]
                }
            );

            var el_label = wcdr_elementor__(
                {
                    type: 'label',
                    text: label_text
                }
            );

            tooltip = null;
            switch(rule){
                case 'url_param':
                    tooltip = wcdr_label_factory.tooltip_url_have_param;
                    break;
                case 'metas':
                    tooltip = wcdr_label_factory.tooltip_have_meta_value;
                    break;
            }

            if(tooltip){
                wcdr_add_tooltip({
                    parent: el_label,
                    text: tooltip
                });    
            }

            var el_field_wrapper = wcdr_elementor__(
                {
                    type: 'table',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_'+rule+'_fields_wrapper wcdr_inlie_input_table widefat'
                        }
                    ]
                }
            );

            var to_loop = (value)? value.length : 1; 

            for(var z = 0; z < to_loop; z++){

            var el_field_wrapper_tr = wcdr_elementor__(
                {
                    type: 'tr'
                }
            );
            
            for(var x = 1; x <= 2; x++){        
                var to_add_field = null;
                if(x == 1){
                    to_add_field = wcdr_elementor__(
                        {
                            type: 'input',
                            attributes: [
                                {
                                    attr: 'type',
                                    value: 'text'
                                },  
                                {
                                    attr: 'class',
                                    value: 'wcdr_'+rule+'_rule_con wcdr__rule_con_inline_text'
                                },
                                {
                                    attr: 'name',
                                    value: 'wcdr_field['+rule+'-'+amount_uid+'][]'
                                },
                                {
                                    attr: 'placeholder',
                                    value: input_key
                                },
                                {
                                    attr: 'value',
                                    value: (value) ? value[z].key : ''
                                }
                            ]
                        }
                    );
                }else{
                    to_add_field = wcdr_elementor__(
                        {
                            type: 'input',
                            attributes: [
                                {
                                    attr: 'type',
                                    value: 'text'
                                },  
                                {
                                    attr: 'class',
                                    value: 'wcdr_'+rule+'_rule_con wcdr__rule_con_inline_text'
                                },
                                {
                                    attr: 'name',
                                    value: 'wcdr_field['+rule+'-'+amount_uid+'][]'
                                },
                                {
                                    attr: 'placeholder',
                                    value: input_value
                                },
                                {
                                    attr: 'value',
                                    value: (value) ? value[z].value : ''
                                }
                            ]
                        }
                    );
                }
                var el_field_td = wcdr_elementor__(
                    {
                        type: 'td',
                        attributes: [
                            {
                                attr: 'width',
                                value: '45%'
                            }
                        ]
                    }
                );
                el_field_td.appendChild(to_add_field);
                el_field_wrapper_tr.appendChild(el_field_td);
            }
            
            var el_field_td_last = wcdr_elementor__(
                {
                    type: 'td',
                    attributes: [
                        {
                            attr: 'width',
                            value: '10%'
                        }
                    ]
                }  
            );     

            //add add button
            var el_add_btn = wcdr_elementor__(
                {
                    type: 'span',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'button button-primary wcdr-add-'+rule+'-el'
                        },
                        {
                            attr: 'title',
                            value: wcdr_label_factory.add
                        }
                    ],
                    text: '+'
                }
            )
            var el_remove_btn = wcdr_elementor__(
                {
                    type: 'span',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'button button-secondary wcdr-remove-'+rule+'-el'
                        },
                        {
                            attr: 'title',
                            value: wcdr_label_factory.remove
                        }
                    ],
                    text: '-'
                }
            )
            el_field_td_last.appendChild(el_add_btn);
            el_field_td_last.appendChild(el_remove_btn);
            el_field_wrapper_tr.appendChild(el_field_td_last); 
            el_field_wrapper.appendChild(el_field_wrapper_tr);

            //end of loop
            }

            parent.appendChild(el_label);
            parent.appendChild(el_field_wrapper);
            jQuery(wcdr_rule_canvas).append(parent);
            wcdr_create_conditions(parent);
    
            break;
            
        default:

            break;
        //pro features will follow
    }


}

function wcdr_elementor__(args = new Object){
    
    if(Object.keys(args).length == 0)
        return;
    
    var element = document.createElement(args.type);
    var text = (args.text)? document.createTextNode(args.text) : document.createTextNode('');
    if(args.attributes){
        for(var x in args.attributes){
            element.setAttribute(args.attributes[x].attr, args.attributes[x].value);
        }
    }
    element.appendChild(text);
    if(args.type == 'select'){
        //create options
        if(args.options){
            for(var y in args.options){
                var option = document.createElement('option');
                option.value = args.options[y].value;
                option.text = args.options[y].text;
                
                var check_value = Array.isArray(args.value);
                if(check_value){
                    for(var b in args.value){
                        if(args.value[b] == args.options[y].value){
                            option.defaultSelected = true;
                        }
                    }
                }else{
                    if(args.value == args.options[y].value){
                        option.defaultSelected = true;
                    }
                }
                element.appendChild(option);
            }
        }
    }
    return element;
}

function wcdr_add_el_to_list(type = null, obj = null)
{
    if(!type || !obj)
        return;

    //create table if not existing and place it next or near relative product options
    var list_parent = jQuery(obj).closest('div.wcdr__rule_con');
    var product = jQuery(obj).val();
    var product_text = jQuery(obj).find('option:selected').text();
    wcdr_generate_added_el_to_list(list_parent, type, product, product_text);

}

function wcdr_generate_added_el_to_list(list_parent, type, product, product_text)
{
    var class_type = 'wcdr_'+type+'_generated_list';
    var table_added = jQuery(list_parent).find('.wcdr_'+type+'_generated_list');
    if(table_added.length == 0){
         //don't forget to get the parent element
        var unique_name = wcdr_unique_name();
        var table = wcdr_elementor__(
            {
                type: 'table',
                attributes: [
                    {
                        attr: 'class',
                        value: class_type+' widefat wcdr_generated_list'
                    },
                    {
                        attr: 'data-list-id',
                        value: unique_name
                    }
                ]
            }
        );
    }
    
    if(list_parent){
        //add if existing
        //get the product details and add to list
        var product = product;
        var product_text = product_text;

        //get child table and unique name
        var child_table = (unique_name)? unique_name : jQuery(list_parent).find('table.'+class_type).data('list-id');

        var tr = wcdr_elementor__(
            {
                type: 'tr'
            }
        );
        var td = wcdr_elementor__(
            {
                type: 'td',
                attributes: [
                    {
                        attr: 'data-product-id',
                        value: product
                    },
                    {
                        attr: 'width',
                        value: '90%'
                    }
                ],
                text: product_text
            }
        );
        
        var hidden_field = wcdr_elementor__(
            {
                type: 'input',
                attributes: [
                    {
                        attr: 'type',
                        value: 'hidden'
                    },
                    {
                        attr: 'value',
                        value: product+'-'+product_text
                    },
                    {
                        attr: 'name',
                        value: 'wcdr_field['+type+'-'+child_table+'][]'
                    }
                ]
            }
        );
        td.appendChild(hidden_field);

        var td_remove = wcdr_elementor__(
            {
                type: 'td',
                attributes: [
                    {
                        attr: 'width',
                        value: '10%'
                    }
                ]
            }
        );
        var td_remove_el = wcdr_elementor__(
            {
                type: 'a',
                attributes:[
                    {
                        attr: 'href',
                        value: 'javascript:void(0);'
                    },
                    {
                        attr: 'class',
                        value: 'wcdr_remove_list_from_list'
                    },
                    {
                        attr: 'data-product-id',
                        value: product
                    }
                ],
                text: wcdr_label_factory.remove
            }
        );
        td_remove.appendChild(td_remove_el);
        tr.appendChild(td);
        tr.appendChild(td_remove);
        if(table_added.length > 0){
            table_added.append(tr);
        }else{
            table.appendChild(tr);
            list_parent.append(table);
        }
        
        
        
    }else{
        return;
    }
}

function wcdr_create_conditions(obj){
    //remove this garbage
    //var get_con = jQuery('.wcdr_rules_canvas__').find('.wcdr__rule_con');

    var el_con_div = wcdr_elementor__(
        {
            type: 'div',
            attributes: [
                {
                    attr: 'class',
                    value: 'wcdr_rule_condition'
                }
            ]
        }
    );

    var el_con_label = wcdr_elementor__(
        {
            type: 'label',
            text: wcdr_label_factory.condition
        }
    );


    var ooptions = [
        {
            text: wcdr_label_factory.and,
            value: 'and'
        }
    ];

    if( codecorun_is_upgraded ){
        ooptions.push( {
            text: wcdr_label_factory.or,
            value: 'or'
        } );
    }

    var el_con_field = wcdr_elementor__(
        {
            type: 'select',
            attributes: [
                {
                    attr: 'class',
                    value: 'wcdr_condition_field'
                },
                {
                    attr: 'name',
                    value: 'wcdr_field[condition-'+wcdr_unique_name()+']'
                }
            ],
            options: ooptions
        }
    );
    el_con_label.appendChild(el_con_field);
    el_con_div.appendChild(el_con_label);
    jQuery(obj).prepend(el_con_div);

     //add remove button
     var el_con_field = wcdr_elementor__(
        {
            type: 'a',
            attributes: [
                {
                attr: 'id',
                value: 'wcdr_condition_remove_field'
                },
                {
                    attr: 'class',
                    value: 'wcdr_condition_remove_field'
                },
                {
                    attr: 'href',
                    value: 'javascript:void(0);'

                }
            ],
            text: wcdr_label_factory.remove
        }
    );

    jQuery(obj).prepend(el_con_field);

    wcdr_remove_first_condition();
}

function wcdr_remove_first_condition()
{
    jQuery('.wcdr_rules_canvas__').find('.wcdr__rule_con').each(function(index){
        if(index == 0){
            jQuery(this).find('.wcdr_rule_condition').remove();
        }
    });
}

function wcdr_init_select2(field_class = null)
{
    if(!field_class)
        return;

    var transkey = jQuery('#wcdr_discount_rules').data('trans-key');

    jQuery(field_class.el).selectWoo({
        minimumInputLength: 3,
        ajax: {
            url: wcdrAjax.ajaxurl,
            data: function (params) {
                var query = {
                    search: params.term,
                    action: field_class.action,
                    nonce: transkey
                }
                return query;
            },
            processResults: function( data ) {
              
				var options = [];
				if ( data ) {
			
					var parsed = JSON.parse(data);
                    for(var x in parsed){
                        options.push(
                            {
                                id: parsed[x].id,
                                text: parsed[x].text
                            }
                        );
                    } 
				
				}
				return {
					results: options
				};
			},
			cache: true
        }
    });

}

function wcdr_create_select_list(params){


    var field_list_cass = 'wcdr_product_list';
    var field_title_type = wcdr_label_factory.product;
    var title_ = (params.type == 'include')? wcdr_label_factory.include : wcdr_label_factory.exclude;
    var field_select_placeholder = wcdr_label_factory.select_product;
    var list_action = 'wcdr_product_list_options';

    var is_multiple = false;

    switch(params.type){
        case 'include_category':
        case 'exclude_category':
            field_list_cass = 'wcdr_category_list';
            field_title_type = wcdr_label_factory.category;
            title_ = (params.type == 'include_category')? wcdr_label_factory.include : wcdr_label_factory.exclude;
            field_select_placeholder = wcdr_label_factory.select_category;
            list_action = 'wcdr_category_list_options';
            is_multiple = true;
            break;
        case 'had_purchased_product':
            title_ = wcdr_label_factory.had_purchased;
            field_title_type = wcdr_label_factory.items;
            break;
        case 'role':
            field_list_cass = 'wcdr_role_list';
            title_ = wcdr_label_factory.role;
            field_title_type = '';
            field_select_placeholder = wcdr_label_factory.select_role;
            list_action = 'wcdr_role_list_options';
            is_multiple = true;
            break;
        default:
            break;
    }

    var parent = wcdr_elementor__(
        {
            type: 'div',
            attributes: [
                {
                    attr: 'class',
                    value: 'wcdr_'+params.type+'_rule_con wcdr__rule_con'
                },
                {
                    attr: 'data-rule-type',
                    value: params.rule
                }
            ]
        }
    );


    
    
    var rule_label = wcdr_elementor__(
        {
            type: 'h4',
            text: title_+ ' ' + field_title_type
        }
    );

    var tooltip_title = null;
    switch(params.type){
        case 'include':
            tooltip_title = wcdr_label_factory.tooltip_include_products;
            break;
        case 'exclude':
            tooltip_title = wcdr_label_factory.tooltip_exclude_products;
            break;
        case 'include_category':
            tooltip_title = wcdr_label_factory.tooltip_include_category;
            break;
        case 'exclude_category':
            tooltip_title = wcdr_label_factory.tooltip_exclude_category;
            break;
        case 'had_purchased_product':
            tooltip_title = wcdr_label_factory.tooltip_had_purchased_items;
            break;
        case 'role':
            tooltip_title = wcdr_label_factory.tooltip_have_roles;
            break;
    }

    if(tooltip_title){
        wcdr_add_tooltip({
            parent: rule_label,
            text: tooltip_title
        });
    }   

    var select__attr_ = [
        {
            attr: 'class',
            value: 'wcdr_'+params.type+'_rule_field widefat '+field_list_cass
        },
        {
            attr: 'data-list-type',
            value: params.type
        }
    ];

    if(is_multiple){
        select__attr_.push({
            attr: 'multiple',
            value: true
        });
        
        select__attr_.push({
            attr: 'name',
            value: 'wcdr_field['+params.type+'-'+wcdr_unique_name()+'][]'
        });
    }

    var default_options = [
        {
            text: field_select_placeholder,
            value: ''
        }
    ];

    var default_value = (params.value)? params.value : []; 
    if(params.type == 'role' || params.type == 'include_category' || params.type == 'exclude_category'){
        for(var c in params.value){
            default_options.push(
                {
                    text: params.value[c],
                    value: params.value[c]
                }
            );
        }
    }

    var el_include = wcdr_elementor__(
        {
            type: 'select',
            attributes: select__attr_,
            options: default_options,
            value: default_value
        }
    );

    parent.appendChild(rule_label);
    parent.appendChild(el_include);
    jQuery(wcdr_rule_canvas).append(parent);
    wcdr_create_conditions(parent);
    wcdr_init_select2({
        el: '.'+field_list_cass,
        action: list_action
    });
    return parent;
}

function wcdr_unique_name()
{
    return Date.now().toString() + Math.random().toString(10).substring(2);
}

function wcdr_load_saved_rules()
{
    var wcdr_get_saved_rules = jQuery('#wcdr_saved_rules_container').text();

    if(!wcdr_get_saved_rules)
        return;

    wcdr_get_saved_rules = JSON.parse(wcdr_get_saved_rules);

    var condition_holder = [];

    for(var x_ in wcdr_get_saved_rules){
        var type = x_.split('-');
        if(type.length > 0){

            if( type[0] == 'include' || 
                type[0] == 'exclude' ||
                type[0] == 'had_purchased_product'
            ){
                
                var product_list = [];
                if(wcdr_get_saved_rules[x_].length > 0){
                    for(var y in wcdr_get_saved_rules[x_]){
                        var product_ = wcdr_get_saved_rules[x_][y].split(/-(.*)/s);
                        product_list.push(
                            {
                                product_id: product_[0],
                                product_text: product_[1]
                            }
                        );
                    }
                }
                wcdr_add_rules(type[0], null, product_list);

            }else if(type[0] == 'date_range'){
                var range = [];
                for(var z in wcdr_get_saved_rules[x_]){
                    range.push(wcdr_get_saved_rules[x_][z]);
                }
                wcdr_add_rules('date-range', range);

            }else if(type[0] != 'condition'){
                wcdr_add_rules(type[0], wcdr_get_saved_rules[x_]);
            }else if(type[0] == 'metas' || type[0] == 'url_param'){

            }else{
                condition_holder.push(wcdr_get_saved_rules[x_]);
            }

        }
    }

    //assign the conditions separately through mapping
    wcdr_assign_saved_conditions(condition_holder);
    
}

function wcdr_assign_saved_conditions(conditions)
{
    jQuery('.wcdr_rules_canvas__').find('select.wcdr_condition_field').each(function(index){
        jQuery(this).val(conditions[index]);
    });
}


function wcdr_add_tooltip(args = null){
    if(!args)
        return;

    var tooltip_icon = wcdr_elementor__(
        {
            type: 'span',
            attributes: [
                {
                    attr: 'class',
                    value: 'wcdr_tooltip dashicons dashicons-editor-help'
                }
            ]
        }
    );

    var tooltip_text = wcdr_elementor__(
        {
            type: 'i',
            text: args.text
        }
    );
    
    tooltip_icon.appendChild(tooltip_text);
    args.parent.appendChild(tooltip_icon);

}