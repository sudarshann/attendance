
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      initialDate: new Date(),
      navLinks: true, // can click day/week names to navigate views
      businessHours: true, // display business hours
      editable: true,
      selectable: true,
      // events: [
      //   {
      //     title: 'Business Lunch',
      //     start: '2020-06-03T13:00:00',
      //     constraint: 'businessHours'
      //   },
      //   {
      //     title: 'Meeting',
      //     start: '2020-06-13T11:00:00',
      //     constraint: 'availableForMeeting', // defined below
      //     color: '#257e4a'
      //   },
      //   {
      //     title: 'Conference',
      //     start: '2020-06-18',
      //     end: '2020-06-20'
      //   },
      //   {
      //     title: 'Party',
      //     start: '2020-06-29T20:00:00'
      //   },

      //   // areas where "Meeting" must be dropped
      //   {
      //     groupId: 'availableForMeeting',
      //     start: '2020-06-11T10:00:00',
      //     end: '2020-06-11T16:00:00',
      //     display: 'background'
      //   },
      //   {
      //     groupId: 'availableForMeeting',
      //     start: '2020-06-13T10:00:00',
      //     end: '2020-06-13T16:00:00',
      //     display: 'background'
      //   },

        // red areas where no events can be dropped
      //   {
      //     start: '2020-06-24',
      //     end: '2020-06-28',
      //     overlap: false,
      //     display: 'background',
      //     color: '#ff9f89'
      //   },
      //   {
      //     start: '2020-06-06',
      //     end: '2020-06-08',
      //     overlap: false,
      //     display: 'background',
      //     color: '#ff9f89'
      //   }
      // ]
    });

    calendar.render();
  });


  jQuery(document).ready(function($){

    $("#calendar ").on("click",".fc-daygrid-day",function(e){
      
      var obj=$(this).children(".fc-daygrid-day-frame").children(".fc-daygrid-day-top").children("a").data("navlink");
      var d= $.each(obj, function(index,value) {
          if(index=="date")
          {
            $(".model_attadance .date_value").text(value);
          }           
      });
      var date= $(".model_attadance .date_value").text();
      var data = '';

      editor="";
      
      $(' .att_table').dataTable().fnDestroy();
      // if ( $.fn.dataTable.isDataTable( '.att_table' ) ) { 
       $(" .att_table").DataTable({
              dom: "Bfrtip",
              buttons: [
                     'excel',
                    // { extend: "create", editor: editor },
                    // { extend: "edit",   editor: editor },
                    // { extend: "remove", editor: editor }
              ],
               select: true,
              "bProcessing": true,
              "serverSide": true,
              "columns": [
                        {
                            "data": "SI",
                            "title": "SI",
                             searchable: true,
                             className:"id"
                        },
                        {
                            "data": "Name",
                            "title": "Name",
                             searchable: true,
                             className:"name"
                        },
                        {
                            "data": "Check in",
                            "title": "Check in",
                            searchable: true,
                            className:"checkin"
                        },
                        {
                            "data": "Check Out",
                            "title": "Check Out",
                            searchable: true,
                            className:"checkout"
                        },
                        {
                            "data": "Date",
                            "title": "Date",
                            searchable: true,
                            className:"date"
                        },
                        {
                            "data": "Delete",
                            "title": "Delete",
                        },
                        {
                            "data": "Edit",
                            "title": "Edit",
                        }            
                    ],
                
              "ajax":{
                  "url":myAjax.ajaxurl+"?action=get_checkin_list&date="+date,
                  "type": "get",  // type of method  , by default would be get
                  "contentType": "application/json",
                  "error": function(){  // error handling code
                    $("#employee_grid_processing").css("display","none");
                  },
              }
      });
     // }


      // $.ajax({
      //    type : "GET",
      //    url : myAjax.ajaxurl,
      //    data : {action: "get_checkin_list",date:date},
      //    success: function(response) {
      //       if(response==0)
      //       {
      //           $(".tbody").html("");
      //       }
      //       else
      //       {
      //           $(".tbody").html(response);
      //       }
      //    }
      // });  
      $(".model_attadance").modal();
     
    });
     $(".att_table").on("click",".delete",function(e){
         
          var id=$(this).parent("td").parent("tr").children(".id").text();

          $.ajax({
            url:myAjax.ajaxurl+"?action=delete_checkin&id="+id,
            method:"GET",
            success:function(msg)
            {
              alert(msg);
            }
          });
      });
    
      $(".att_table").on("click",".edit",function(e){
          $(".editmodel").modal();
          var id=$(this).parent("td").parent("tr").children(".id").text();
          var checkin=$(this).parent("td").parent("tr").children(".checkin").text();
          var checkout=$(this).parent("td").parent("tr").children(".checkout").text();
          $(".checkin_edit").val(checkin);     
          $(".checkout_edit").val(checkout);
          $(".id_edit").val(id);
      });
    $(".attandance_record").click( function(e) {
            var checkin=$(".checkin").val();
            var checkout=$(".checkout").val();
            var date_value=$(".date_value").text();
            var user_id_list={};

            $('select[multiple].active.3col option').each(function(index,level){
                user_id_list[index]=$(this).val();
            });
            
            $.ajax({
               type : "GET",
               url : myAjax.ajaxurl,
               data : {action: "create_checkin", checkin : checkin, checkout: checkout,user_id_list:user_id_list,date_value:date_value},
               success: function(response) {
                    // $(".tbody").html(response);
                return 0;
               }
            });          
     });
   $(".attandance_record").click( function(e) {
      var checkin=$(".checkin").val();
      var checkout=$(".checkout").val();
      var date_value=$(".date_value").text();
      var user_id_list={};

      $('select[multiple].active.3col option').each(function(index,level){
          user_id_list[index]=$(this).val();
      });
  
      $.ajax({
         type : "GET",
         url : myAjax.ajaxurl,
         data : {action: "create_checkin", checkin : checkin, checkout: checkout,user_id_list:user_id_list,date_value:date_value},
         success: function(response) {
              // $(".tbody").html(response);
            alert(response);
         }
      }); 
   });


   $(function () {
          $('select[multiple].active.3col').multiselect({
              columns: 3,
              placeholder: 'Select States',
              search: true,
              searchOptions: {
                  'default': 'Search States'
              },
              selectAll: true
          });
    });

    // find the input fields and apply the time select to them.
    $('.checkin').ptTimeSelect();
    $('.checkout').ptTimeSelect();
    $('.checkin_edit').ptTimeSelect();
    $('.checkout_edit').ptTimeSelect();

   
 });  