@if(!empty($roles->users))
@php $i =1; @endphp
@foreach($roles->users as $user)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->phone_number}}</td>
            <td>@if(isset($roles->name)){{$roles->name}}@endif</td>
            <td>
                @isset($user->getCreatedBy->name) {{$user->getCreatedBy->name}} 
                @else Self (Mobile App)
                @endisset
            </td>
            <td> 

                @can('user-list')
                    <a href="{{route('user.detail',['id' => $user->id])}}">
                        <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-view" data-toggle="tooltip" data-original-title="User Detail"><i class="icon-user" aria-hidden="true"></i>
                    </button></a>
                @endcan

                @if(isset($roles->name) && !in_array($roles->name,['Super-Admin','User']) || empty($user->roles[0]))  
                    @can('user-edit')
                        <a href="{{route('user.edit',['id' => $user->id])}}">
                        <button class="btn btn-sm btn-icon btn-pure btn-default on-default m-r-5 button-edit" data-toggle="tooltip" data-original-title="Edit"><i class="icon-pencil" aria-hidden="true"></i> 
                        </button></a>
                    @endcan    
                @endif
                
                @can('user-delete')
                <a href="{{route('user.delete',['id' => $user->id])}}" onclick="return confirm('Are you sure want to delete?')">
                    <button class="btn btn-sm btn-icon btn-pure btn-default on-default button-remove" data-toggle="tooltip" data-original-title="Remove"><i class="icon-trash" aria-hidden="true"></i>
                </button></a>
                @endcan
            </td>
        </tr> 
        @endforeach
        @endif