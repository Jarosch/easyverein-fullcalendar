jQuery(
    function ($) {
  
        // Memberlist
        $("#easyVerein_sortable_memberlist").sortable(
            {
                stop: function (e, ui) {
                    $("#easyVerein_memberlist_order").val(
                        $.map(
                            $(this).find('li'), function (el) {
                                return $(el).attr('id');
                            }
                        )
                    );
                    $("#easyVerein_save_memberlist").prop("disabled", false);
                }
            }
        );

        $('#easyVerein_memberlist_header_color,#easyVerein_memberlist_header_font_color,#easyVerein_memberlist_table_color,#easyVerein_memberlist_table_font_color').change(
            function () {
                $("#easyVerein_save_memberlist").prop("disabled", false);
            }
        );

        // Groups
        $("#easyVerein_sortable_groups").sortable();

        // Sort memberlist
        let ul = $("#easyVerein_sortable_memberlist");
        let custumOrder = $("#easyVerein_memberlist_order").val();
        custumOrder = custumOrder.split(",");
        for (let item of custumOrder) {
               ul.append($('#' + item + ''));
        }

        $('.easyVerein_sortableItem_isvisible').click(
            function () {
                $("#easyVerein_save_memberlist").prop("disabled", false);
            }
        );

        // API Key
        $('#easyVerein_api_key').click(
            function () {
                $("#easyVerein_save_api_key").prop("disabled", false);
            }
        );

        // Calendar
        $('#easyVerein_calendar_background_color,#easyVerein_calendar_text_color,#easyVerein_calendar_limit').click(
            function () {
                $("#easyVerein_save_calendar").prop("disabled", false);
            }
        );

        // Protocol
        $('#easyVerein_protocol_background_color,#easyVerein_protocol_text_color,#easyVerein_protocol_limit').click(
            function () {
                $("#easyVerein_save_protocol").prop("disabled", false);
            }
        );

        //Login Form
        $('#easyVerein_login_form_url,#easyVerein_login_form_button_color,#easyVerein_login_form_button_text_color').click(
            function () {
                $("#easyVerein_save_login_form").prop("disabled", false);
            }
        );

        //Login Form
        $('#easyVerein_ssobutton_button_color,#easyVerein_ssobutton_button_text_color').click(
            function () {
                $("#easyVerein_save_ssobutton_form").prop("disabled", false);
            }
        );
    }
);