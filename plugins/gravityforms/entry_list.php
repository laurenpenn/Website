<?php
class GFEntryList{
    public static function all_leads_page(){

        if(!GFCommon::ensure_wp_version())
            return;

        $forms = RGFormsModel::get_forms(null, "title");
        $id = RGForms::get("id");

        if(sizeof($forms) == 0)
        {
            ?>
            <div style="margin:50px 0 0 10px;">
                <?php echo sprintf(__("You don't have any active forms. Let's go %screate one%s", "gravityforms"), '<a href="?page=gravityforms.php&id=0">', '</a>'); ?>
            </div>
            <?php
        }
        else{
            if(empty($id))
                $id = $forms[0]->id;

            self::leads_page($id);
        }
    }

    public static function leads_page($form_id){
        global $wpdb;

        //quit if version of wp is not supported
        if(!GFCommon::ensure_wp_version())
            return;

        echo GFCommon::get_remote_message();
        $action = RGForms::post("action");

        switch($action){
            case "delete" :
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $lead_id = $_POST["action_argument"];
                RGFormsModel::delete_lead($lead_id);
            break;

            case "bulk":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $bulk_action = !empty($_POST["bulk_action"]) ? $_POST["bulk_action"] : $_POST["bulk_action2"];
                $leads = $_POST["lead"];
                switch($bulk_action){
                    case "delete":
                        RGFormsModel::delete_leads($leads);
                    break;

                    case "mark_read":
                        RGFormsModel::update_leads_property($leads, "is_read", 1);
                    break;

                    case "mark_unread":
                        RGFormsModel::update_leads_property($leads, "is_read", 0);
                    break;

                    case "add_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 1);
                    break;

                    case "remove_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 0);
                    break;
                }
            break;

            case "change_columns":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $columns = GFCommon::json_decode(stripslashes($_POST["grid_columns"]), true);
                RGFormsModel::update_grid_column_meta($form_id, $columns);
            break;
        }

        $sort_field = empty($_GET["sort"]) ? 0 : $_GET["sort"];
        $sort_direction = empty($_GET["dir"]) ? "DESC" : $_GET["dir"];
        $search = RGForms::get("s");
        $page_index = empty($_GET["paged"]) ? 0 : intval($_GET["paged"]) - 1;
        $star = is_numeric(RGForms::get("star")) ? intval(RGForms::get("star")) : null;
        $read = is_numeric(RGForms::get("read")) ? intval(RGForms::get("read")) : null;
        $page_size = 20;
        $first_item_index = $page_index * $page_size;

        $form = RGFormsModel::get_form_meta($form_id);
        $sort_field_meta = RGFormsModel::get_field($form, $sort_field);
        $is_numeric = $sort_field_meta["type"] == "number";

        $leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search, $first_item_index, $page_size, $star, $read, $is_numeric);
        $lead_count = RGFormsModel::get_lead_count($form_id, $search, $star, $read);

        $summary = RGFormsModel::get_form_counts($form_id);
        $total_lead_count = $summary["total"];
        $unread_count = $summary["unread"];
        $starred_count = $summary["starred"];


        $columns = RGFormsModel::get_grid_columns($form_id, true);

        $search_qs = empty($search) ? "" : "&s=" . urlencode($search);
        $sort_qs = empty($sort_field) ? "" : "&sort=$sort_field";
        $dir_qs = empty($sort_field) ? "" : "&dir=$sort_direction";
        $star_qs = $star !== null ? "&star=$star" : "";
        $read_qs = $read !== null ? "&read=$read" : "";

        $page_links = paginate_links( array(
            'base' =>  admin_url("admin.php") . "?page=gf_entries&view=entries&id=$form_id&%_%" . $search_qs . $sort_qs . $dir_qs. $star_qs . $read_qs,
            'format' => 'paged=%#%',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($lead_count / $page_size),
            'current' => $page_index + 1,
            'show_all' => false
        ));


        wp_print_scripts(array("thickbox"));
        wp_print_styles(array("thickbox"));


        ?>

        <script src="<?php echo GFCommon::get_base_url() ?>/js/jquery.json-1.3.js?ver=<?php echo GFCommon::$version ?>"></script>

        <script>
            function ChangeColumns(columns){
                jQuery("#action").val("change_columns");
                jQuery("#grid_columns").val(jQuery.toJSON(columns));
                tb_remove();
                jQuery("#lead_form")[0].submit();
            }

            function Search(sort_field_id, sort_direction, form_id, search, star, read){
                var search_qs = search == "" ? "" : "&s=" + search;
                var star_qs = star == "" ? "" : "&star=" + star;
                var read_qs = read == "" ? "" : "&read=" + read;

                var location = "?page=gf_entries&view=entries&id=" + form_id + "&sort=" + sort_field_id + "&dir=" + sort_direction + search_qs + star_qs + read_qs;
                document.location = location;
            }

            function ToggleStar(img, lead_id){
                var is_starred = img.src.indexOf("star1.png") >=0
                if(is_starred)
                    img.src = img.src.replace("star1.png", "star0.png");
                else
                    img.src = img.src.replace("star0.png", "star1.png");

                UpdateCount("star_count", is_starred ? -1 : 1);

                UpdateLeadProperty(lead_id, "is_starred", is_starred ? 0 : 1);
            }

            function ToggleRead(lead_id){
                var title = jQuery("#lead_row_" + lead_id);

                marking_read = title.hasClass("lead_unread");

                jQuery("#mark_read_" + lead_id).css("display", marking_read ? "none" : "inline");
                jQuery("#mark_unread_" + lead_id).css("display", marking_read ? "inline" : "none");
                title.toggleClass("lead_unread");

                UpdateCount("unread_count", marking_read ? -1 : 1);

                UpdateLeadProperty(lead_id, "is_read", marking_read ? 1 : 0);
            }

            function UpdateLeadProperty(lead_id, name, value){
                var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_update_lead_property" );
                mysack.setVar( "rg_update_lead_property", "<?php echo wp_create_nonce("rg_update_lead_property") ?>" );
                mysack.setVar( "lead_id", lead_id);
                mysack.setVar( "name", name);
                mysack.setVar( "value", value);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php echo esc_js(__("Ajax error while setting lead property", "gravityforms")) ?>' )};
                mysack.runAJAX();

                return true;
            }

            function UpdateCount(element_id, change){
                var element = jQuery("#" + element_id);
                var count = parseInt(element.html()) + change
                element.html(count + "");
            }

            function DeleteLead(lead_id){
                jQuery("#action").val("delete");
                jQuery("#action_argument").val(lead_id);
                jQuery("#lead_form")[0].submit();
                return true;
            }



            jQuery(document).ready(function(){
                jQuery("#lead_search").keyup(function(event){
                  if(event.keyCode == 13)
                    Search('<?php echo $sort_field ?>', '<?php echo $sort_direction ?>', <?php echo $form_id ?>, this.value, '<?php echo $star ?>', '<?php echo $read ?>');
                });

            });

        </script>
        <link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin.css" type="text/css" />
        <style>
            .lead_unread a, .lead_unread td{font-weight: bold;}
            .row-actions a{ font-weight:normal;}
            .entry_nowrap{
                overflow:hidden; white-space:nowrap;
            }
        </style>


        <div class="wrap">
            <img alt="<?php _e("Gravity Forms", "gravityforms") ?>" src="<?php echo GFCommon::get_base_url()?>/images/gravity-entry-icon-32.png" style="float:left; margin:15px 7px 0 0;"/>
            <h2><?php _e("Entries", "gravityforms"); ?> : <?php echo $form["title"] ?> </h2>

            <?php RGForms::top_toolbar() ?>

            <form id="lead_form" method="post">
                <?php wp_nonce_field('gforms_entry_list', 'gforms_entry_list') ?>

                <input type="hidden" value="" name="grid_columns" id="grid_columns" />
                <input type="hidden" value="" name="action" id="action" />
                <input type="hidden" value="" name="action_argument" id="action_argument" />

                <ul class="subsubsub">
                    <li><a class="<?php echo ($star === null && $read === null) ? "current" : "" ?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>"><?php _e("All", "gravityforms"); ?> <span class="count">(<span id="all_count"><?php echo $total_lead_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $read !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&read=0"><?php _e("Unread", "gravityforms"); ?> <span class="count">(<span id="unread_count"><?php echo $unread_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $star !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&star=1"><?php _e("Starred", "gravityforms"); ?> <span class="count">(<span id="star_count"><?php echo $starred_count ?></span>)</span></a></li>
                </ul>
                <p class="search-box">
                    <label class="hidden" for="lead_search"><?php _e("Search Entries:", "gravityforms"); ?></label>
                    <input type="text" id="lead_search" value="<?php echo $search ?>"><a class="button" id="lead_search_button" href="javascript:Search('<?php echo $sort_field ?>', '<?php echo $sort_direction ?>', <?php echo $form_id ?>, jQuery('#lead_search').val(), '<?php echo $star ?>', '<?php echo $read ?>');"><?php _e("Search", "gravityforms") ?></a>
                </p>
                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>

                            <?php if(GFCommon::current_user_can_any("gravityforms_delete_entries")){ ?>
                            <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            <?php } ?>

                            <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                            <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                            <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                            <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);
                        ?>

                    </div>

                    <?php
                    //Displaying paging links if appropriate
                    if($page_links){
                        ?>
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
                            <?php echo $page_links ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="clear"></div>
                </div>

                <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style="vertical-align:middle;"><input type="checkbox" class="headercb" /></th>
                        <th scope="col" class="manage-column column-cb check-column" >&nbsp;</th>
                        <?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column entry_nowrap" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>');" style="cursor:pointer;"><?php echo esc_html($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" align="right" width="50">
                            <a title="<?php _e("Select Columns" , "gravityforms") ?>" href="<?php echo GFCommon::get_base_url() ?>/select_columns.php?id=<?php echo $form_id ?>&TB_iframe=true&height=365&width=600" class="thickbox entries_edit_icon">Edit</a>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" >&nbsp;</th>
                        <?php
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column entry_nowrap" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>');" style="cursor:pointer;"><?php echo esc_html($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" style="width:15px;">
                            <a href="<?php echo GFCommon::get_base_url() ?>/select_columns.php?id=<?php echo $form_id ?>&TB_iframe=true&height=350&width=500" class="thickbox entries_edit_icon">Edit</a>
                        </th>
                    </tr>
                </tfoot>

                <tbody class="list:user user-list">
                    <?php
                    if(sizeof($leads) > 0){
                        $field_ids = array_keys($columns);

                        foreach($leads as $lead){
                            ?>
                            <tr id="lead_row_<?php echo $lead["id"] ?>" class='author-self status-inherit <?php echo $lead["is_read"] ? "" : "lead_unread" ?>' valign="top">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="lead[]" value="<?php echo $lead["id"] ?>" />
                                </th>
                                <td >
                                    <img src="<?php echo GFCommon::get_base_url() ?>/images/star<?php echo intval($lead["is_starred"]) ?>.png" onclick="ToggleStar(this, <?php echo $lead["id"] ?>);" />
                                </td>
                                <?php
                                $is_first_column = true;

                                $nowrap_class="entry_nowrap";
                                foreach($field_ids as $field_id){
                                    $value = RGForms::get($field_id, $lead);

                                    //filtering lead value
                                    $value = apply_filters("gform_get_field_value", $value, $lead, RGFormsModel::get_field($form, $field_id));

                                    $input_type = !empty($columns[$field_id]["inputType"]) ? $columns[$field_id]["inputType"] : $columns[$field_id]["type"];
                                    switch($input_type){
                                        case "checkbox" :
                                            $value = "";

                                            //looping through lead detail values trying to find an item identical to the column label. Mark with a tick if found.
                                            $lead_field_keys = array_keys($lead);
                                            foreach($lead_field_keys as $input_id){
                                                //mark as a tick if input label (from form meta) is equal to submitted value (from lead)
                                                if(is_numeric($input_id) && absint($input_id) == absint($field_id)){
                                                    if($lead[$input_id] == $columns[$field_id]["label"]){
                                                        $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                    }
                                                    else{
                                                        $field = RGFormsModel::get_field($form, $field_id);
                                                        if(rgar($field, "enableChoiceValue") || rgar($field, "enablePrice")){
                                                            foreach($field["choices"] as $choice){
                                                                if($choice["value"] == $lead[$field_id]){
                                                                    $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                                    break;
                                                                }
                                                                else if($field["enablePrice"]){
                                                                    $ary = explode("|", $lead[$field_id]);
                                                                    $val = count($ary) > 0 ? $ary[0] : "";
                                                                    $price = count($ary) > 1 ? $ary[1] : "";

                                                                    if($val == $choice["value"]){
                                                                        $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        break;

                                        case "post_image" :
                                            list($url, $title, $caption, $description) = explode("|:|", $value);
                                            if(!empty($url)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($url);
                                                $value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "post_category" :
                                            $ary = explode(":", $value);
                                            $cat_name = count($ary) > 0 ? $ary[0] : "";

                                            $value = $cat_name;
                                        break;

                                        case "fileupload" :
                                            $file_path = $value;
                                            if(!empty($file_path)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($file_path);
                                                $file_path = esc_attr($file_path);
                                                $value = "<a href='$file_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "source_url" :
                                            $value = "<a href='" . esc_attr($lead["source_url"]) . "' target='_blank' alt='" . esc_attr($lead["source_url"]) ."' title='" . esc_attr($lead["source_url"]) . "'>.../" . esc_attr(GFCommon::truncate_url($lead["source_url"])) . "</a>";
                                        break;

                                        case "textarea" :
                                        case "post_content" :
                                        case "post_excerpt" :
                                            $value = esc_html($value);
                                        break;

                                        case "date_created" :
                                        case "payment_date" :
                                            $value = GFCommon::format_date($value, false);
                                        break;

                                        case "date" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = GFCommon::date_display($value, $field["dateFormat"]);
                                        break;

                                        case "radio" :
                                        case "select" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = GFCommon::selection_display($value, $field, $lead["currency"]);
                                        break;

                                        case "total" :
                                        case "payment_amount" :
                                            $value = GFCommon::to_money($value, $lead["currency"]);
                                        break;

                                        case "created_by" :
                                            if(!empty($value)){
                                                $userdata = get_userdata($value);
                                                $value = $userdata->user_login;
                                            }
                                        break;

                                        default:
                                            $value = esc_html($value);
                                    }

                                    $value = apply_filters("gform_entries_field_value", $value, $form_id, $field_id, $lead);

                                    $query_string = "gf_entries&view=entry&id={$form_id}&lid={$lead["id"]}{$search_qs}{$sort_qs}{$dir_qs}&paged=" . $page_index + 1;
                                    if($is_first_column){
                                        ?>
                                        <td class="column-title" >
                                            <a href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs?>&paged=<?php echo ($page_index + 1)?>"><?php echo $value ?></a>
                                            <div class="row-actions">
                                                <span class="edit">
                                                    <a title="<?php _e("View this entry", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs?>&paged=<?php echo ($page_index + 1)?>"><?php _e("View", "gravityforms"); ?></a>
                                                    |
                                                </span>
                                                <span class="edit">
                                                    <a id="mark_read_<?php echo $lead["id"] ?>" title="Mark this entry as read" href="javascript:ToggleRead(<?php echo $lead["id"] ?>);" style="display:<?php echo $lead["is_read"] ? "none" : "inline" ?>;"><?php _e("Mark read", "gravityforms"); ?></a><a id="mark_unread_<?php echo $lead["id"] ?>" title="<?php _e("Mark this entry as unread", "gravityforms"); ?>" href="javascript:ToggleRead(<?php echo $lead["id"] ?>);" style="display:<?php echo $lead["is_read"] ? "inline" : "none" ?>;"><?php _e("Mark unread", "gravityforms"); ?></a>
                                                    <?php echo GFCommon::current_user_can_any("gravityforms_delete_entries") ? "|" : "" ?>
                                                </span>

                                                <?php if(GFCommon::current_user_can_any("gravityforms_delete_entries"))
                                                {
                                                    ?>
                                                    <span class="edit">
                                                        <?php
                                                        $delete_link ='<a title="' . __("Delete this entry", "gravityforms"). '"  href="javascript:if ( confirm(' . __("'You are about to delete this entry. \'Cancel\' to stop, \'OK\' to delete.'", "gravityforms"). ') ) { DeleteLead(' . $lead["id"] .')};">' . __("Delete", "gravityforms") .'</a>';
                                                        echo apply_filters("gform_delete_entry_link", $delete_link);
                                                        ?>
                                                    </span>
                                                    <?php
                                                }

                                                do_action("gform_entries_first_column_actions", $form_id, $field_id, $value, $lead, $query_string);
                                                ?>

                                            </div>
                                            <?php
                                            do_action("gform_entries_first_column", $form_id, $field_id, $value, $lead, $query_string);
                                            ?>
                                        </td>
                                        <?php

                                    }
                                    else{
                                        ?>
                                        <td class="<?php echo $nowrap_class ?>">
                                            <?php echo $value ?>&nbsp;
                                            <?php
                                            do_action("gform_entries_column", $form_id, $field_id, $value, $lead, $query_string);
                                            ?>
                                        </td>
                                        <?php
                                    }
                                    $is_first_column = false;
                                }
                                ?>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                        }
                    }
                    else{
                        ?>
                        <tr>
                            <td colspan="<?php echo sizeof($columns) + 3 ?>" style="padding:20px;"><?php _e("This form does not have any entries yet.", "gravityforms"); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                </table>

                <div class="clear"></div>

                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action2"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action2" id="bulk_action2">
                        <option value=''><?php _e("Bulk action ", "gravityforms") ?></option>
                            <option value='delete'><?php _e("Delete", "gravityforms") ?></option>
                            <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                            <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                            <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                            <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="jQuery(\'#action\').val(\'bulk\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);
                        ?>
                    </div>

                    <?php
                    //Displaying paging links if appropriate
                    if($page_links){
                        ?>
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php printf(__("Displaying %d - %d of %d", "gravityforms"), $first_item_index + 1, ($first_item_index + $page_size) > $lead_count ? $lead_count : $first_item_index + $page_size , $lead_count) ?></span>
                            <?php echo $page_links ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="clear"></div>
                </div>

            </form>
        </div>
        <?php
    }

    private static function get_icon_url($path){
        $info = pathinfo($path);

        switch(strtolower($info["extension"])){

            case "css" :
                $file_name = "icon_css.gif";
            break;

            case "doc" :
                $file_name = "icon_doc.gif";
            break;

            case "fla" :
                $file_name = "icon_fla.gif";
            break;

            case "html" :
            case "htm" :
            case "shtml" :
                $file_name = "icon_html.gif";
            break;

            case "js" :
                $file_name = "icon_js.gif";
            break;

            case "log" :
                $file_name = "icon_log.gif";
            break;

            case "mov" :
                $file_name = "icon_mov.gif";
            break;

            case "pdf" :
                $file_name = "icon_pdf.gif";
            break;

            case "php" :
                $file_name = "icon_php.gif";
            break;

            case "ppt" :
                $file_name = "icon_ppt.gif";
            break;

            case "psd" :
                $file_name = "icon_psd.gif";
            break;

            case "sql" :
                $file_name = "icon_sql.gif";
            break;

            case "swf" :
                $file_name = "icon_swf.gif";
            break;

            case "txt" :
                $file_name = "icon_txt.gif";
            break;

            case "xls" :
                $file_name = "icon_xls.gif";
            break;

            case "xml" :
                $file_name = "icon_xml.gif";
            break;

            case "zip" :
                $file_name = "icon_zip.gif";
            break;

            case "gif" :
            case "jpg" :
            case "jpeg":
            case "png" :
            case "bmp" :
            case "tif" :
            case "eps" :
                $file_name = "icon_image.gif";
            break;

            case "mp3" :
            case "wav" :
            case "wma" :
                $file_name = "icon_audio.gif";
            break;

            case "mp4" :
            case "avi" :
            case "wmv" :
            case "flv" :
                $file_name = "icon_video.gif";
            break;

            default:
                $file_name = "icon_generic.gif";
            break;
        }

        return GFCommon::get_base_url() . "/images/doctypes/$file_name";
    }
}
?>
