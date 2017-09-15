$(function() {
    $("#tasksTable").DataTable({
        processing: true,
        serverSide: true,
        iDisplayLength: 3,
        columnDefs: [{
            targets: 6,
            orderable: false
        },{
            targets: 7,
            orderable: false
        }],
        aLengthMenu: [[3, 10, 50, -1], [3, 10, 50, "All"]],
    });
});