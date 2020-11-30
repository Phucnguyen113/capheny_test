function add_product(){
    var product_id=$('#product_id').val();
    var color=$('#color').val();
    var size=$('#size').val();
    var product_amount=$('#product_amount').val();
    var price=p_price;
    var validate=true;
    var error=[];
   if(product_id=='0' || product_id==undefined || product_id==null){
       validate=false;
       error[0]='Chưa chọn sản phẩm'
   }
   if(color=='0' || color==undefined || color==null){
       validate=false;
       error[1]='Chưa chọn màu'
   }
   if(size=='0' || size==undefined || size==null){
       validate=false;
       error[2]='Chưa chọn kích cỡ'
   }
   if(product_amount!==''  && product_amount!==null && product_amount!==undefined ){
       if(error[3]== undefined){
           if(isNaN(product_amount)){
               validate=false;
               error[3]='Số lượng phải là số'
           }else{
               if(product_amount<=0){
                   validate=false;
                   error[3]='Số lượng phải lớn hơn 0'
               }
           }
       }
   }else{
       validate=false;
       error[3]='Chưa nhập số lượng'
   }
   if(price==null || price==undefined || price==''){
       validate=false; 
   }
   $('.p_error').html('')
   if(!validate){
       var index_key=[];
       index_key[0]='product_id_form';
       index_key[1]='color_form';
       index_key[2]='size_form';
       index_key[3]='product_amount_form';
       $.each(error,function(index,item){
           console.log('oke');
           $(`#${index_key[index]}_error`).html(item);
       })
       return;
   }
   var product_name=$('#product_id option:selected').text();
   var color_name=$('#color option:selected').attr('data-color');
   var size_name=$('#size option:selected').text();
   if(product_amount>100){
       product_amount=100
   }
   // $('#product_id').val('0').trigger('change')
   $('#color').val('0').trigger('change')
   $('#size').val('0').trigger('change')
   $('#product_amount').val('')
   var check_distinct=false;
   $.each($('#table_tbody_product .tbody_tr'),function(index,item){
       if($(this).find('input[name="product_id"]').val()==product_id && $(this).find('input[name="color"]').val()==color && $(this).find('input[name="size"]').val()==size){
           var amount=Number($(this).find('.product_amount_td span').text());
           var content=`<span id="amount_text_${product_id}_${color}_${size}">${Number(product_amount)+amount}</span> <button type="button" onclick="up_amount('${product_id}_${color}_${size}')" class="btn btn-success">+</button> <button type="button" onclick="down_amount('${product_id}_${color}_${size}')" class="btn btn-dark">+</button><input type="hidden" id="product_${product_id}_${color}_${size}" name="product_amount" value="${Number(product_amount)+amount}">`
           $(this).find('.product_amount_td').html(content)
           check_distinct=true;
       };
   })
   if(!check_distinct){
       var html='';
       html+=`<tr class="tbody_tr">`
       html+=`<td>${product_name} <input type="hidden" name="product_id" value="${product_id}"> </td>`
       html+=`<td><div style="width:15px;height:15px;background-color:#${color_name}"></div> <input type="hidden" name="color" value="${color}"></td>`
       html+=`<td>${size_name} <input type="hidden" name="size" value="${size}"></td>`
       html+=`<td class="product_amount_td"><span id="amount_text_${product_id}_${color}_${size}">${product_amount}</span> <button type="button" onclick="up_amount('${product_id}_${color}_${size}')" class="btn btn-success">+</button> <button type="button" onclick="down_amount('${product_id}_${color}_${size}')" class="btn btn-dark">-</button><input type="hidden" id="product_${product_id}_${color}_${size}" name="product_amount" value="${product_amount}"></td>`
       html+=`<td>${price} VND <input type="hidden" name="price" value="${price}" ></td>`
       html+=`<td><button  id="remove_product_${product_id}_${color}_${size}" onclick="remove_product('${product_id}_${color}_${size}')" type="button" class="btn btn-danger remove_product"><i class="fa fa-trash-alt"></i></button></td>`
       $('#table_tbody_product').append(html)
   }
}
function up_amount(key){
    var amount=Number( $(`#product_${key}`).val() )+1;
    if(amount<=100){
        $(`#product_${key}`).val(amount);
        $(`#amount_text_${key}`).text(amount);
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Không thể tăng số lượng !',
            text: 'Số lượng tối đa là 100',
        })
    }
    
}
function down_amount(key){
    var amount=Number( $(`#product_${key}`).val() )-1;
    if(amount>0){
        $(`#product_${key}`).val(amount);
        $(`#amount_text_${key}`).text(amount);
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Không thể giảm số lượng !',
            text: 'Số lượng tối thiểu là 1',
        })
    }
   
}