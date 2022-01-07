jQuery(document).ready(function(){
    jQuery('#wcdr_select_rules__').change(function(){
        var rule = jQuery(this).val();
        jQuery(this).val('');
        wcdr_add_rules(rule);
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
    jQuery('body').on('change','.wcdr_product_list',function(){
        var type = jQuery(this).data('list-type');
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
            var inc_parent = wcdr_create_include_exclude_list(
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
            var exc_parent = wcdr_create_include_exclude_list(
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
        case 'count':

            var c_value = [];
            for(var v in value){
                c_value.push(value[v]);
            }
        
            var count_uid = wcdr_unique_name();
            var parent = wcdr_elementor__(
                {
                    type: 'div',
                    attributes: [
                        {
                            attr: 'class',
                            value: 'wcdr_count_rule_con wcdr__rule_con'
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
                    text: wcdr_label_factory.items_in_cart
                }
            );

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
                            value: 'wcdr_field[count-'+count_uid+'][condition]'
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
                    value: (c_value.length > 0)? c_value[0] : value
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
                            value: 'wcdr_count_rule_el wcdr_number_el'
                        },
                        {
                            attr: 'placeholder',
                            value: '0'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field[count-'+count_uid+'][value]'
                        },
                        {
                            attr: 'value',
                            value: (c_value.length > 0)? c_value[1] : value
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
        case 'amount':

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
                            value: 'wcdr_amount_rule_con wcdr__rule_con'
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
                    text: wcdr_label_factory.total_amount
                }
            );

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
                            value: 'wcdr_field[amount-'+amount_uid+'][condition]'
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
                            value: 'wcdr_amount_rule_el wcdr_number_el'
                        },
                        {
                            attr: 'placeholder',
                            value: '0'
                        },
                        {
                            attr: 'name',
                            value: 'wcdr_field[amount-'+amount_uid+'][value]'
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
        default:
            jQuery('.wcdr_no_rules').show();
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
                if(args.options[y].text == 'Or'){
                    option.disabled = true;
                }
                
                if(args.value == args.options[y].value){
                    option.defaultSelected = true;
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
    var class_type = (type == 'include')? 'wcdr_include_generated_list' : 'wcdr_exclude_generated_list';
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
                text: 'Remove'
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
    var get_con = jQuery('.wcdr_rules_canvas__').find('.wcdr__rule_con');

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
            options: [
                {
                    text: wcdr_label_factory.and,
                    value: 'and'
                },
                {
                    text: wcdr_label_factory.or,
                    value: 'or'
                }
            ]
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

    jQuery(field_class).selectWoo({
        minimumInputLength: 3,
        ajax: {
            url: wcdrAjax.ajaxurl,
            data: function (params) {
                var query = {
                    search: params.term,
                    action: 'wcdr_product_list_options',
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

function wcdr_create_include_exclude_list(params){
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


    var title_ = (params.type == 'include')? wcdr_label_factory.include : wcdr_label_factory.exclude;
    
    var rule_label = wcdr_elementor__(
        {
            type: 'h4',
            text: title_+ ' ' + wcdr_label_factory.product
        }
    );

    var el_include = wcdr_elementor__(
        {
            type: 'select',
            attributes: [
                {
                    attr: 'class',
                    value: 'wcdr_'+params.type+'_rule_field widefat wcdr_product_list'
                },
                {
                    attr: 'data-list-type',
                    value: params.type
                }
            ],
            options: [
                {
                    text: 'Select Product',
                    value: ''
                }
            ]
        }
    );

    parent.appendChild(rule_label);
    parent.appendChild(el_include);
    jQuery(wcdr_rule_canvas).append(parent);
    wcdr_create_conditions(parent);
    wcdr_init_select2('.wcdr_product_list');
    return parent;
}

function wcdr_unique_name()
{
    return Date.now().toString() + Math.random().toString(10).substring(2);
}

function wcdr_load_saved_rules()
{
    var wcdr_get_saved_rules = jQuery('#wcdr_saved_rules_container').text();
    wcdr_get_saved_rules = JSON.parse(wcdr_get_saved_rules);

    var condition_holder = [];

    for(var x_ in wcdr_get_saved_rules){
        var type = x_.split('-');
        if(type.length > 0){

            if(type[0] == 'include' || type[0] == 'exclude'){
                
                var product_list = [];
                if(wcdr_get_saved_rules[x_].length > 0){
                    for(var y in wcdr_get_saved_rules[x_]){
                        var product_ = wcdr_get_saved_rules[x_][y].split('-');
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