@extends('layouts.admin')
@section('body')
    <a href="{{url()->previous()}}" style="color:white " class="btn btn-warning mb-2">Quay lại</a>
    <div class="main-card card">
        <div class="card-body">
            <div class="card-title" style="font-size:36px;text-align:center">Chỉnh sửa danh mục</div>
            <form class="form-group" action="{{url('admin/category')}}" onsubmit="return add_category()" method="POST" enctype="multipart/form-data"> 
                @csrf
                <div class="form-row">
                    <div class="col-md-8">
                        <div class="position-relative form-group">
                            <label for="category_name" class="">Tên danh mục <span style="color:Red"> *</span></label>
                            <input name="category_name"  value="{{$cate->category_name}}" onkeyup="convert_vn_to_en()" id="category_name" placeholder="Nhập tên danh mục ở đây" type="text" class="form-control">
                            <div id="category_name_error" style="color:red"></div>
                        </div>
                        
                    </div>
                    
                     <!-- category  -->
                     <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="category" class="">Danh mục cha <span style="color:Red"> *</span></label>
                            <input name="category" id="category" placeholder="Đường dẫn sản phẩm" readonly='true' type="text" class="form-control">
                            <div id="category_error" class="p_error"></div>
                            <script>
                                var data=[];
                                $.ajax({
                                    type: "post",
                                    url: "{{url('category/tree_category/select/')}}/{{$cate->category_id}}",
                                    data: {_token:$('input[name="_token"]').val()},
                                    dataType: "json",
                                    async:false,
                                    success: function (response) {
                                        if(!$.isEmptyObject(response.error)){
                                            console.log(response.error);
                                        }else{
                                            data=response
                                        }
                                    }
                                });
                                data.push({id:0,title:'Không có danh mục cha'})
                                console.log(data);
                                var category=$('#category').comboTree({
                                    source : data,
                                    isMultiple: false,
                                    cascadeSelect: true,
                                    collapse: false,
                                    selected:['{{$cate->category_parent_id}}']
                                });
                            </script>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <label for="category_slug" class="">Đường dẫn danh mục <span style="color:Red"> *</span></label>
                            <input name="category_slug" value="{{$cate->category_slug}}" id="category_slug"  type="text" class="form-control">
                            <div id="category_slug_error" style="color:red"></div>
                        </div>
                    </div>
                </div>
               <div class="form-row form-group">
                   <div class="col-md-12">
                       
                        <button class=" btn btn-primary">Sửa</button>
                   </div>  
               </div>
                
                </form>
            </div> 
        </div>
            <script>
                function get_slug_parent(){
                    var category_parent_id=$('input[name="category_parent_id"]:checked').val();
                    var _token= $('input[name="_token"]').val();
                    $.ajax({
                        type: "post",
                        url: "{{url('admin/category/slug')}}/"+category_parent_id,
                        data: {_token:_token,category_parent_id:category_parent_id},
                        dataType: "json",
                        success: function (response) {
                            console.log(response);
                        }
                    });
                }
                function convert_vn_to_en(){
                    console.log('keyup');
                    var oldtext=$('#category_name').val().trim();
                    var newtext = oldtext.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
                        newtext = newtext.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, 'A');
                        newtext = newtext.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
                        newtext = newtext.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, 'E');
                        newtext = newtext.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
                        newtext = newtext.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, 'I');
                        newtext = newtext.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,'o');
                        newtext = newtext.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g,'O');
                        newtext = newtext.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
                        newtext = newtext.replace(/Ù|ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, 'U');
                        newtext = newtext.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,'y');
                        newtext = newtext.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g,'Y');
                        newtext = newtext.replace(/đ/g, 'd');
                        newtext = newtext.replace(/Đ/g, 'D');
                        newtext = newtext.replace(/\s/g, '-');                    
                       $('#category_slug').val(newtext)
                }
                function add_category(){
                    var category_name= $('#category_name').val().trim();
                    var value_category=category.getSelectedIds();
                    if(value_category!==null)  var category_parent_id=value_category[0];
                    else   var category_parent_id=null;
                    var category_slug=$('#category_slug').val().trim();
                    var _token= $('input[name="_token"]').val();
                    $.ajax({
                        type: "PUT",
                        url: "{{url('admin/category/')}}/{{$cate->category_id}}",
                        data: {_token:_token,category_slug:category_slug,category_name:category_name,category_parent_id:Number(category_parent_id)},
                        dataType: "json",
                        success: function (response) {
                                console.log(response);
                                if(!$.isEmptyObject(response.error)){
                                    console.log(response.error);
                                    $('.p_error').html('')
                                    $.each(response.error,function(index,item){
                                        $('#'+index+'_error').html(item)
                                    })
                                }else{
                                    Swal.fire({
                                        icon:'success',
                                        title:'Sửa thành công!',
                                        text:'Bạn vừa chỉnh sửa danh mục'
                                    }).then(()=>{
                                        window.location.href='{{url("admin/category")}}'
                                    })
                                }
                        }
                    });
                    return false;
                }

            </script>
@endsection