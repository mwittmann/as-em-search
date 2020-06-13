<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
    global $wpdb;

    if( ! empty( $_GET ) && isset( $_GET['as_em_search'] ) ) {
        $search_results = as_em_search( $_GET['as_em_search'] );
    } else {
        $search_results = null;
    }
    $as_em_status_array = array(
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
        3 => 'Cancelled',
        4 => 'Awaiting Payment'
    );
?>
<h1><?php _e('Search bookings','as-em-search');?></h1>
<form method="get">
    <input type="hidden" name="post_type" value="<?php echo EM_POST_TYPE_EVENT;?>">
    <input type="hidden" name="page" value="events-manager-search-form">
    <input type="text" name="as_em_search" value>
    <button type="submit"><?php _e('Search','as-em-search');?></button>
</form>

<div>
    <?php if( null != $search_results ): ?>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
                <tr>
                    <th><?php _e('Name', 'as-em-search');?></th>
                    <th><?php _e('Email', 'as-em-search');?></th>
                    <th><?php _e('City','as-em-search');?></th>
                    <th><?php _e('Country','as-em-search');?></th>
                    <th><?php _e('Phone','as-em-search');?></th>
                    <th><?php _e('Spaces','as-em-search');?></th>
                    <th><?php _e('Booked On','as-em-search');?></th>
                    <th><?php _e('Status','as-em-search');?></th>
                </tr>
            </thead>
            <tbody>
            <?php $event_id = -1; ?>
            <?php foreach( $search_results as $result ): ?>
                <?php
                    $meta = maybe_unserialize( $result->booking_meta );
                    $meta = $meta['registration'];
                ?>
                <?php if( $event_id != $result->event_id ):?>
                    <?php
                        $event_id = $result->event_id;
                        $event_info = as_em_get_event_info( $event_id );
                    ?>
                    <tr>
                        <td colspan="3" class="as_evtname"><h3><?php echo $event_info['event_name'];?></h3></td>
                        <td colspan="5" style="vertical-align:middle;" class="as_evtstart">
                            starts on: <?php echo $event_info['event_start_date'].' '.$event_info['event_start_time'].' (local) / '.$event_info['event_start'].' UTC';?>
                        </td>
                    </tr>
                <?php endif;?>
                <tr>
                    <td>
                        <a href="edit.php?post_type=<?php echo EM_POST_TYPE_EVENT;?>&page=events-manager-bookings&event_id=<?php echo $event_id;?>&booking_id=<?php echo $result->booking_id;?>">
                        <?php
                            if( isset( $meta['last_name'] ) ) {
                                echo $meta['last_name'] . ' ' .  $meta['first_name'];
                            } else {
                                echo $meta['user_name'];
                            }
                        ?>
                        </a>
                    </td>
                    <td>
                        <?php echo $meta['user_email'];?>
                    </td>
                    <td>
                        <?php echo $meta['dbem_city'];?>
                    </td>
                    <td>
                        <?php echo $meta['dbem_country'];?>
                    </td>
                    <td>
                        <?php echo $meta['dbem_phone'];?>
                    </td>
                    <td>
                        <?php echo $result->booking_spaces;?>
                    </td>
                    <td>
                        <?php echo $result->booking_date;?>
                    </td>
                    <td>
                        <?php echo ($result->booking_status < 4 ? $as_em_status_array[$result->booking_status] : "Awaiting Payment (#$result->booking_status)"); ?>
                    </td>
                </tr>
            <?php endforeach;?>
            <?php if( count($search_results) > 100 ):?>
                <tr>
                    <td colspan="8">Only the first 100 results are shown</td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
    <?php elseif( ! empty( $_GET ) && isset( $_GET['as_em_search'] ) ): ?>
        <p>No results for "<?php echo $_GET['as_em_search']?>".</p>
    <?php else: ?>
        <p>Enter your search term above.&nbsp; Up to 100 rows are returned, sorted by most recent events and most recent bookings first.</p>
    <?php endif; ?>
</div>