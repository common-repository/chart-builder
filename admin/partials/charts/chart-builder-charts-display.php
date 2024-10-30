<?php
$items = $this->db_obj->get_items();
$all_items = CBFunctions()->get_all_charts_count();
$chart_count_per_page = count($items) > 0 ? $all_items/$this->db_obj->get_pagination_count() : 0;
$chart_paged = isset($_GET['paged']) && $_GET['paged'] != '' ? absint( sanitize_text_field($_GET['paged'])) : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php
        echo __( esc_html( get_admin_page_title() ), "chart-builder" );
        echo sprintf( '<a href="?page=%s&action=%s" class="btn btn-primary mx-2 chart-add-new-bttn">' . __( 'Add New', "chart-builder" ) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        // $this->surveys_categories_obj->views();
                    ?>
                    <form method="post">
                        <?php
                            // $this->surveys_categories_obj->prepare_items();
                            // $this->surveys_categories_obj->display();
                        ?>
                        <table class="chart-list-table table">
                            <thead>
                                <tr>
                                    <th class="column-cb">
                                        <input type="checkbox" class="form-check-input select-all" value="" />
                                    </th>
                                    <th class="column-title"><?php echo esc_html(__( 'Title', "chart-builder" )); ?></th>
                                    <th class="column-type"><?php echo esc_html(__( 'Type', "chart-builder" )); ?></th>
                                    <th class="column-shortcode"><?php echo esc_html(__( 'Shortcode', "chart-builder" )); ?></th>
                                    <th class="column-author"><?php echo esc_html(__( 'Author', "chart-builder" )); ?></th>
                                    <th class="column-status"><?php echo esc_html(__( 'Status', "chart-builder" )); ?></th>
                                    <th class="column-id"><?php echo esc_html(__( 'ID', "chart-builder" )); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ( !empty( $items ) ): ?>
                            <?php foreach ( $items as $key => $item ): ?>
                                <tr>
                                    <td class="column-cb">
                                        <input type="checkbox" class="form-check-input check-current-row" name="bulk-delete[]" value="<?php echo esc_attr($item['id']) ?>" />
                                    </td>
                                    <td class="column-title"><?php
                                        if($item['status'] == 'trashed'){
                                            $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-item' );
                                        }else{
                                            $delete_nonce = wp_create_nonce( $this->plugin_name . '-trash-item' );
                                        }
                                        $chart_title = stripcslashes( $item['title'] );
                                        $q = esc_attr( $chart_title );
                                        $title = sprintf( '<a href="?page=%s&action=%s&id=%d" title="%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, stripcslashes($item['title']));

                                        $actions = array();
                                        if($item['status'] == 'trashed'){
                                            $title = sprintf( '<strong><a>%s</a></strong>', $chart_title );
                                            $actions['restore'] = sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">'. __('Restore', "chart-builder") .'</a>', esc_attr( $_REQUEST['page'] ), 'restore', absint( $item['id'] ), $delete_nonce );
                                            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&id=%s&_wpnonce=%s">'. __('Delete Permanently', "chart-builder") .'</a>', $chart_title, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
                                        }else{
                                            $draft_text = '';
                                            if( $item['status'] == 'draft' && !( isset( $_GET['fstatus'] ) && $_GET['fstatus'] == 'draft' )){
                                                $draft_text = ' — ' . '<span class="post-state">' . __( "Draft", "chart-builder" ) . '</span>';
                                            }
                                            $title = sprintf( '<strong><a href="?page=%s&action=%s&id=%d" title="%s">%s</a>%s</strong>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, $chart_title, $draft_text );

                                            $actions['edit'] = sprintf( '<a href="?page=%s&action=%s&id=%d">'. __('Edit', "chart-builder") .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) );
                                            $actions['delete'] = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">'. __('Delete', "chart-builder") .'</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
                                        }
                                        echo wp_kses_post($title);

                                        echo '<p class="chart-list-table-actions-row">';
                                        foreach ( $actions as $action => $action_html ){
                                            $link_class = '';
                                            if( $action == 'delete' ){
                                                $link_class = 'link-danger';
                                            }
                                            echo '<span class="chart-list-table-action-link ' . $link_class . '">' . $action_html . '</span>';
                                        }
                                        echo '</p>';
                                    ?></td>
                                    <td class="column-type"><?php
		                                switch ($item['source_chart_type']) {
			                                case 'bar_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/bar-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Bar Chart', "chart-builder") . "</span></p>";
				                                break;

			                                case 'pie_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/pie-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Pie Chart', "chart-builder") . "</span></p>";
				                                break;

			                                case 'column_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/column-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Column Chart', "chart-builder") . "</span></p>";
				                                break;
		                                }
                                    ?></td>
                                    <td class="column-shortcode">
<!--                                        <input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="--><?//= esc_attr('[ays_chart id="'. $item['id'] .'"]') ?><!--" />-->
                                        <div class="ays-chart-shortcode-container">
                                            <div class="ays-chart-copy-image" data-toggle="tooltip" title="<?php echo esc_html(__('Click for copy.',"chart-builder"));?>">
                                                <img src='<?php echo esc_url(CHART_BUILDER_ADMIN_URL) . "/images/icons/copy-image.svg" ?>'>
                                            </div>
                                            <!-- <input type="text" class="ays-chart-shortcode-input" onClick="this.setSelectionRange(0, this.value.length)" readonly value="<//?= esc_attr('[ays_chart id="'. $item['id'] .'"]') ?>" /> -->
                                            <input type="text" class="ays-chart-shortcode-input" readonly value="<?php echo esc_attr('[ays_chart id="'. $item['id'] .'"]') ?>" />
                                        </div>
                                    </td>
                                    <td class="column-author"><?php
		                                $author = get_user_by("id", $item['author_id']);
                                        if( $author ){
                                            $author_name = $author->data->display_name;
                                            echo esc_html($author_name);
                                        }
                                    ?></td>
                                    <td class="column-status"><?php
                                        $status = ucfirst( $item['status'] );
                                        $date = date( 'Y/m/d', strtotime( $item['date_modified'] ) );
                                        $title_date = date( 'l jS \of F Y h:i:s A', strtotime( $item['date_modified'] ) );
                                        $html = "<p style='font-size:14px;margin:0;'>" . $status . "</p>";
                                        $html .= "<p style=';font-size:14px;margin:0;text-decoration: dotted underline;' title='" . $title_date . "'>" . $date . "</p>";
//	                                    $html = "<p style=';font-size:14px;margin:0;text-decoration: dotted underline;' title='" . $title_date . "'>" . $date . "</p>";
                                        
                                		echo wp_kses_post( $html );
                                    ?></td>
                                    <td class="column-id"><?php echo esc_attr($item['id']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7"><?php echo esc_html(__( 'There are no items yet.', "chart-builder" )); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <?php if($chart_count_per_page > 1):?>
                                    <tr>
                                        <td colspan="7">
                                            <nav aria-label="Pagination" class="m-0">
                                                <ul class="pagination m-0 p-2">
                                                <?php for( $i = 0; $i < $chart_count_per_page; $i++ ):
                                                    $url = esc_url_raw( remove_query_arg( false ) );
                                                    $url = esc_url_raw( add_query_arg( array(
                                                        'paged' => $i + 1
                                                    ), $url ) );

                                                    $active = '';
                                                    if( $chart_paged != '' ){
                                                        if( $chart_paged == $i + 1 ) {
                                                            $active = 'active';
                                                        }
                                                    } else {
	                                                    wp_safe_redirect($_SERVER['REQUEST_URI'] . "&paged=1");
                                                    }
	                                                ?>
                                                    <li class="page-item <?php echo esc_attr($active) ?>">
                                                        <a class="page-link" href="<?php echo esc_url($url) ?>"><?php echo esc_attr(absint($i)) + 1; ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                </ul>
                                            </nav>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <h1 class="wp-heading-inline">
		<?php
		echo __( esc_html( get_admin_page_title() ), "chart-builder" );
		echo sprintf( '<a href="?page=%s&action=%s" class="btn btn-primary mx-2 chart-add-new-bttn">' . __( 'Add New', "chart-builder" ) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
		?>
    </h1>
</div>
