var total_page = 0;
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-center",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}

/**
 * Calculate line number to print in template.
 * 
 * @param {int} i index of current line
 * @param {int} page current page
 */
function get_index(i, page) {
    return parseInt(i) + ((parseInt(page) - 1) * 10) + 1
}

/**
 * Send XmlHttpRequest to back-end to fetch json result from server.
 * Compile and print it using Backbone/Underscore template
 * 
 * @param {int} page requested page number
 */
function fetchData(page) {
    var path = $('#path').val();
    if (!path) a = 10;
    if (typeof (page) === 'undefined') page = $('#page').val();

    $.ajax({
        url: "get_log.php?path=" + path + "&page=" + page,
        accepts: 'json',
        dataType: 'json',
        beforeSend: function () {
            toastr["info"]("Fetching your requested log file", "Loading");
        },
        success: function (data) {
            if (data.logs) {
                list_view = $("#log-item-template").html();
                var compiled = _.template(list_view);
                $('#log-panel').html(compiled(data));
                $('#page').val(data.page);
                total_page = data.total_page;
                $('#content').show();
            }
            if (data.page == total_page) {
                $('#nxt').attr("disabled", "disabled");
            } else {
                $('#nxt').removeAttr("disabled");
            }
            if (!data.page || data.page == 1) {
                $('#prev').attr("disabled", "disabled");
            } else {
                $('#prev').removeAttr("disabled");
            }
        },
        error: function (data) {
            var json_response = data.responseJSON.error;
            toastr.clear();
            toastr["error"](json_response.msg, "Ooops!");
        },
        complete: function (data, status) {
            if (status != "error") {
                toastr.clear();
            }
        }
    });
}
$(document).ready(function () {
    $('#content').hide();
    // User enters log file location
    $("form").submit(function (e) {
        e.preventDefault();
        fetchData()
    });
    // User paginate
    $('a').on('click', function (e) {
        e.preventDefault();
        switch (e.target.id) {
            case 'fst':
                fetchData(1);
                break;
            case 'prev':
                $('#page').val(parseInt($('#page').val()) - 1);
                fetchData();
                break;
            case 'nxt':
                $('#page').val(parseInt($('#page').val()) + 1);
                fetchData();
                break;
            case 'lst':
                fetchData(total_page);
                break;
        }
    });
});