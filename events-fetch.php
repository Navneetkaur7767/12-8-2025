<?php
/*
 * Plugin Name: Events-fetch Table
 * Description: Learning to make table and menu in admin panel
 * Author: Navneet Kaur
 * Version: 1.0
 * Text Domain: events-fetch
 * 
 */

function dd($var)
    {
    echo '<pre>';
    print_r($var);
    echo '</pre>';

    }
// load the wp_list_table
if (!class_exists('Wp_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    }

// extend wp_list_table
class Events_fetch extends Wp_List_Table
    {
    // define $table_data property
    private $table_data;
    //1. Define the column in the table
    function get_columns()
        {
        $columns = array(
            'cb' => '<input type="checkbox"/>',
            'event_title' => __('Event Name', 'events-fetch'),
            'startdate' => __('Start Date', 'events-fetch'),
            'enddate' => __('End Date', 'events-fetch'),
            'display_name' => __('Username', 'events-fetch')

        );
        return $columns;
        }

    function prepare_items()
        {
        // data is now here
        $this->table_data = $this->get_data_from_database();
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        // $primary = 'event_title';
        $this->_column_headers = array($columns, $hidden, $sortable);
        // Apply sorting ONLY if user requested it
        if (!empty($_GET['orderby']) && !empty($_GET['order'])) {
            usort($this->table_data, array($this, 'usort_reorder'));
            }
        /* pagination */
        $per_page = 7;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page' => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));
        // if   $this->items = []; then table will show till here but data will go in items so that it can display data
        $this->items = $this->table_data;
        }
    // Function to get all the data from the database
    function get_data_from_database()
        {
        global $wpdb;
        $table = $wpdb->prefix . 'events';
        // return $wpdb->get_results("SELECT id,event_title,startdate,enddate,user_id from {$table}", ARRAY_A);
        return $wpdb->get_results("
    SELECT e.id, e.event_title, e.startdate,e.enddate, u.display_name
    FROM {$table} AS e
    JOIN {$wpdb->prefix}users AS u ON e.user_id = u.ID
", ARRAY_A);

        }

    function column_default($item, $column_name)
        {
        switch ($column_name) {
            case 'event_title':
            case 'startdate':
            case 'enddate':
            case 'display_name':
                return $item[$column_name];
            default:
                return print_r($item, true); // fallback for debugging
            }
        }
    function column_cb($item)
        {
        return sprintf(
            '<input type="checkbox" name="event_ids[]" value="%s" />',
            $item['id']
        );
        }
    protected function get_sortable_columns()
        {
        $sortable_columns = array(
            'event_title' => array('event_title', false),
            'startdate' => array('startdate', false),
            'enddate' => array('enddate', false),
            'display_name' => array('display_name', false)
        );
        return $sortable_columns;
        }
    function usort_reorder($a, $b)
        {
        // Which column to sort by (default = event_title)
        // If no sort request, don't sort at all
        if (empty($_GET['orderby'])) {
            return 0; // returning 0 means "leave order as is"
            }
        $orderby = $_GET['orderby'];
        // Ascending or descending? (default = asc)
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Compare the two values
        $result = strcmp($a[$orderby], $b[$orderby]);
        // Reverse result if descending
        return ($order === 'asc') ? $result : -$result;
        }
    }

// adding the events menu in the admin page 
function events_menu_page()
    {
    add_menu_page(
        'Events',        // The text to be displayed in the browser title bar when the page is active.
        'Events',        // The text to be displayed in the admin menu.
        'manage_options',    // The capability required to access this menu. 'manage_options' is typically for administrators.
        'my-events',    // The unique slug for your menu page. Used in the URL.
        'events_layout', // The function that outputs the content of your page.
        'dashicons-admin-generic', // The URL to the icon for this menu item (Dashicon or custom URL).
        21                    // The position in the menu order.
    );
    }
add_action('admin_menu', 'events_menu_page');

function events_layout()
    {
    $table = new Events_fetch();
    echo '<div class="wrap"><h2>Events fetch table</h2>';
    echo '<form method="POST">';
    //  prepare table 
    $table->prepare_items();
    // display table
    $table->display();
    echo '</div></form>';
    }
?>