function add_order(){
    var formdata= new FormData();
    var _token=$("input[name='_token']").val();
    formdata.append('_token',_token)
    formdata.append('user_id',$('#user_id').val());
    formdata.append('order_email',$('#order_email').val())
    formdata.append('order_phone',$('#order_phone').val());
    formdata.append('order_name',$('#order_name').val());
    formdata.append('province',$("#province").val())
    formdata.append('district',$('#district').val())
    formdata.append('order_address',$('#order_address').val())
    formdata.append('ward',$('#ward').val());
    $.each($('#table_tbody_product .tbody_tr'),function(index,item){
        var product=[];
        product['product_id']=$(this).find('input[name="product_id"]').val();
        product['color']=$(this).find('input[name="color"]').val();
        product['size']=$(this).find('input[name="size"]').val();
        product['product_amount']=$(this).find('input[name="product_amount"]').val();
        product['price']=$(this).find('input[name="price"]').val();
        formdata.append('product_id[]',product['product_id'])
        formdata.append('product_color[]',product['color'])
        formdata.append('product_size[]',product['size']);
        formdata.append('product_amount[]',product['product_amount']);
        formdata.append('product_price[]',product['price']);
    })

    $.ajax({
        type: "post",
        url: "{{url('admin/order/create')}}",
        data: formdata,
        dataType: "json",
        contentType:false,
        processData:false,
        success: function (response) {
            console.log(response);
            if(!$.isEmptyObject(response.error)){
                $('.p_error').html('');
                $.each(response.error,function(index,item){
                    if(index=='product_id'){
                        Swal.fire({
                            icon: 'error',
                            title: 'Chưa chọn sản phẩm',
                            text: 'Bạn cần thêm ít nhất 1 sản phẩm',
                        })
                    }else{
                        if(index=='size_500' || index=='color_500' || index=='product_500'){
                            Swal.fire({
                            icon: 'error',
                            title: 'Lỗi! Xin thử lại',
                            text: 'Hãy thử tải lại trang',
                            })
                        }else  $(`#${index}_error`).html(item);
                    }
                   
                })
            }else{
                Swal.fire({
                    icon: 'success',
                    title: 'Tạo mới đơn hàng thành công',
                    text: 'Bạn vừa tạo mới 1 đơn hàng',
                }).then(()=>{
                    window.location.href="{{url('admin/order')}}"
                })
            }
        }
    });
}
// remove product
function remove_product(key){
    console.log('click');
   $(`#remove_product_${key}`).parent().parent().remove();
}