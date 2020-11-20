function view_setting(user_id,table){
    // Swal.fire({
    //     title: "Loading...",
    //     text: "Please wait",
    //     imageUrl: "https://icon-library.com/images/loading-icon-animated-gif/loading-icon-animated-gif-19.jpg",
    //     button: false,
    //     closeOnClickOutside: false,
    //     closeOnEsc: false
    //   });
    var formdata= new FormData();
    $('.view-setting').each(function(index,item){
        if($(this).is(':checked')==true){
             $(`.${$(this).val()}`).show();
             formdata.append($(this).val(),1)
        }else{
             $(`.${$(this).val()}`).hide();
             formdata.append($(this).val(),0)
        }
    })
    formdata.append('user_id',user_id);
    formdata.append('table',table);
     $.ajax({
         type: "POST",
         url: "/api/ui_setting",
         data: formdata,
         processData:false,
         contentType:false,
         dataType: "json",
         success: function (response) {
             console.log(response);
            //  Swal.fire({
            //      icon:'success',
            //     title: "oke",
            //     text: "Please wait",
            //   });
         }
     });
}