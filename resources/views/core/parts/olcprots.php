<td>
  @foreach($permissionsexceptions as $excp)
    @if(
        !empty($excp->id_permission_exception) &&
        $excp->id_menu_item == $menu->id_menu_item &&
        $excp->id_user == $excp->core_permissions_user->id_user
    )
    <p>{{ $excp->permission }}</p>
    @endif
  @endforeach
</td>
<td>
  <button toggle="confirm_update"
    @if(
        $permissionsexceptions[0]->id_permission_exception != 0 &&
        $permissionsexceptions[0]->id_menu_item == $menu->id_menu_item &&
        $permissionsexceptions[0]->id_user == $permissionsexceptions[0]->core_permissions_user->id_user
    )
    data-id-perm-expt="{{ $permissionsexceptions[0]->id_permission_exception }}"
    @else
      data-id-perm-expt="0"
    @endif
      data-id-men-item="{{$menu->id_menu_item or 0}}"
      data-id-user="{{ $permissionsexceptions[0]->id_user or 0}}"
 type="button" class="action_edit btn btn-xs btn-warning" title="Elimina">
 <i class="fas fa-edit"></i></button>
</td>
<?php


  if(
    $permissionsexceptions[0]->id_permission_exception != 0 &&
    $permissionsexceptions[0]->id_menu_item == $menu->id_menu_item &&
    $permissionsexceptions[0]->id_user == $permissionsexceptions[0]->core_permissions_user->id_user
  ){
    var_dump($permissionsexceptions[0]->id_permission_exception);

  } else {
    var_dump('NOOOOO');

  }




 ?>



 /////////////////////////////////////////////////


 <tr class="tr nivel_{{$nivel}} id_menu_item_{{$menu->id_menu_item}} id_parent_{{$menu->id_parent}}">
     <td class="">{{$menu->description or ''}} {{$menu->id_menu_item}}</td>
         <?php $btn_status = 0;?>
 <td>
 @if(count($menu->core_permission) > 0)
 @foreach($permissionsexceptions as $excp)
   @if(
     $excp->id_user == 10 &&
     $excp->id_menu_item == $menu->id_menu_item &&
     !empty($excp->id_permission_exception)
   )
     <p>{{ $excp->permission }}</p>

     <button toggle="confirm_update"
         data-id-perm-expt="{{ $excp->id_permission_exception or 0 }}"
         data-id-men-item="{{$excp->id_menu_item or 0}}"
         data-id-user="{{ $excp->id_user or 0}}"
         type="button" class="action_edit btn btn-xs btn-warning" title="Edita">
         <i class="fas fa-edit"></i>
     </button>
   @else
   <p>gol</p>
   <button toggle="confirm_update"
       data-id-perm-expt="0"
       data-id-men-item="{{$menu->id_menu_item or 0}}"
       data-id-user="10"
       type="button" class="action_edit btn btn-xs btn-warning" title="Edita">
       <i class="fas fa-edit"></i>
   </button>
   @endif




 @endforeach

 </td>




 </tr>
 @if (count($menu->children) > 0)
     <?php $nivel .= 0; ?>
     @foreach($menu->children as $menu)
         @include('core.parts.exceptpermission', ['menu' => $menu,'permissionsexceptions'=> $permissionsexceptions, 'nivel' => $nivel])
     @endforeach
 @endif
