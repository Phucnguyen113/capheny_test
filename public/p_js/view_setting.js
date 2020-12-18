function view_setting(user_id,table){
    Swal.fire({
        title: "Xin chờ",
        text: "Đang thay đổi ",
        willOpen:()=>{
            Swal.showLoading();
        },
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
        timmer:2000,
        showConfirmButton:false
      });
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
         url: "../../public/api/ui_setting",
         data: formdata,
         processData:false,
         contentType:false,
         dataType: "json",
         success: function (response) {
             console.log(response);
           Swal.close();
         }
     });
}