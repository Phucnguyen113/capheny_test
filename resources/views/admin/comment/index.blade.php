@extends('layouts.admin')
@section('body')
    <div class="card card-main">
        <div class="card-body">
            <div class="card-title" style="text-align: center;font-size:36px">Danh sách comment</div>
            @if(p_author('add','tbl_comment'))
                <a href="{{url('admin/comment/create')}}" class="btn btn-success mb-2">Thêm mới bình luận</a>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Bình luận</th>
                            <th>Active</th>
                            <th>Ngày bình luận</th>
                            <th>Ngày sửa</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_comment as $comments => $comment)
                            <tr data-comment="{{$comment->comment_id}}">
                                <td>{{$comment->user_email}}</td>
                                <td>{{$comment->product_name}}</td>
                                <td>{!!Str::limit($comment->content,50)!!}</td>
                                <td>
                                    @if($comment->active==1)
                                        Kích hoạt
                                    @else
                                        Chưa kích hoạt
                                    @endif
                                </td>
                                <td>{{$comment->create_at}}</td>
                                <td>{{$comment->update_at}}</td>
                                <td>
                                    @if(p_author('edit','tbl_comment'))
                                        <a href="{{url('admin/comment')}}/{{$comment->comment_id}}/edit" class="btn btn-info"><div class="fa fa-edit"></div></a>
                                    @endif
                                    @if(p_author('delete','tbl_comment'))
                                    <form style="display:inline-block;"  onsubmit="return delete_comment({{$comment->comment_id}})" action="{{url('admin/comment')}}/{{$comment->comment_id}}/delete" method="post">
                                        @csrf 
                                        <button type="submit" class="btn btn-danger"><div class="fa fa-trash-alt"></div></button>
                                    </form>
                                    
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
    function delete_comment(id){
        var _token=$('input[name="_token"]').val();
        $.ajax({
            type: "POST",
            url: "{{url('admin/comment')}}/"+id+"/delete",
            data:{_token:_token},
            dataType: "json",
            success: function (response) {
                if(!$.isEmptyObject(response.error)){

                }else{
                    Swal.fire({
                        icon:'success',
                        title:'Xóa thành công',
                        text:'Bạn vừa xóa 1 bình luận'
                    }).then(()=>{
                        $('tr[data-comment='+id+']').remove();
                    })
                }
            }
        });
        return false;
    }
</script>
@endsection
@section('css')
@endsection