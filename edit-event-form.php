<?php
// if (!function_exists('submit_button')) {
//     require_once ABSPATH . 'wp-admin/includes/template.php';
//     }
global $wpdb;
$table = $wpdb->prefix . 'events';
$id = intval($_GET['id']);

// Fetch event
$event = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);

if (!$event) {
    echo '<div class="notice notice-error"><p>Event not found.</p></div>';
    return;
    }

// Save changes
if (isset($_POST['save_event'])) {
    $wpdb->update(
        $table,
        array(
            'event_title' => sanitize_text_field($_POST['event_title']),
            'startdate' => sanitize_text_field($_POST['startdate']),
            'enddate' => sanitize_text_field($_POST['enddate']),
            'adddate' => current_time('mysql'), // sets current WP time in MySQL format

        ),
        array('id' => $id),
        array('%s', '%s', '%s', '%s'),
        array('%d')
    );
    echo '<div class="notice notice-success"><p>Event updated successfully.</p></div>';
    }
?>

<div class="wrap">
    <h1>Edit Event</h1>
    <form method="post">
        <table class="form-table">
            <tr>
                <th>Title</th>
                <td><input type="text" name="event_title" value="<?php echo esc_attr($event['event_title']); ?>"
                      required></td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td><input type="date" name="startdate" value="<?php echo esc_attr($event['startdate']); ?>" required>
                </td>
            </tr>
            <tr>
                <th>End Date</th>
                <td><input type="date" name="enddate" value="<?php echo esc_attr($event['enddate']); ?>" required></td>
            </tr>
        </table>
        <?php submit_button('Save Changes', 'primary', 'save_event'); ?>
    </form>
</div>