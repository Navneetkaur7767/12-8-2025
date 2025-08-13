<?php
function bulk_edit_form($ids)
    {
    global $wpdb;
    $table = $wpdb->prefix . 'events';

    // On submission, update all selected events
    if (isset($_POST['bulk_update'])) {
        $new_title = sanitize_text_field($_POST['event_title']);
        $new_startdate = sanitize_text_field($_POST['startdate']);
        $new_enddate = sanitize_text_field($_POST['enddate']);

        foreach ($ids as $id) {
            $wpdb->update(
                $table,
                [
                    'event_title' => $new_title,
                    'startdate' => $new_startdate,
                    'enddate' => $new_enddate,
                ],
                ['id' => $id],
                ['%s', '%s', '%s'],
                ['%d']
            );
            }

        // Redirect back to main page with success message
        wp_redirect(admin_url('admin.php?page=my-events&bulk_updated=1'));
        exit;
        }

    // Fetch current values of first selected event to prefill form
    $first_event = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $ids[0]), ARRAY_A);

    echo '<div class="wrap">';
    echo '<h1>Bulk Edit Events</h1>';
    echo '<form method="post">';

    // Keep the IDs in hidden inputs
    foreach ($ids as $id) {
        echo '<input type="hidden" name="event_ids[]" value="' . esc_attr($id) . '">';
        }

    echo '<table class="form-table">';
    echo '<tr><th><label for="event_title">Event Title</label></th>';
    echo '<td><input type="text" id="event_title" name="event_title" value="' . esc_attr($first_event['event_title']) . '" required></td></tr>';

    echo '<tr><th><label for="startdate">Start Date</label></th>';
    echo '<td><input type="date" id="startdate" name="startdate" value="' . esc_attr($first_event['startdate']) . '" required></td></tr>';

    echo '<tr><th><label for="enddate">End Date</label></th>';
    echo '<td><input type="date" id="enddate" name="enddate" value="' . esc_attr($first_event['enddate']) . '" required></td></tr>';

    echo '</table>';

    submit_button('Update Selected Events', 'primary', 'bulk_update');

    echo '</form>';
    echo '</div>';
    }
?>