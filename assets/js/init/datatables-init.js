(function ($) {
    //    "use strict";


    /*  Data Table
    -------------*/




    $('#bootstrap-data-table').DataTable({
        lengthMenu: [[50, 70, 90, -1], [50, 70, 90, "All"]],
    });
	$('#bootstrap-data-table1').DataTable({
        lengthMenu: [[50, 70, 90, -1], [50, 70, 90, "All"]],
    });
	$('#bootstrap-data-table2').DataTable({
        lengthMenu: [[20, 40, 60, -1], [20, 40, 60, "All"]],
    });

    $('#bootstrap-data-tableteachers-export').DataTable({
        dom: 'lBfrtip',
        lengthMenu: [[50, 70, 90, -1], [50, 70, 90, "All"]],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
	
	$('#row-select').DataTable( {
			initComplete: function () {
				this.api().columns().every( function () {
					var column = this;
					var select = $('<select class="form-control"><option value=""></option></select>')
						.appendTo( $(column.footer()).empty() )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
	 
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );
	 
					column.data().unique().sort().each( function ( d, j ) {
						select.append( '<option value="'+d+'">'+d+'</option>' )
					} );
				} );
			}
		} );






})(jQuery);