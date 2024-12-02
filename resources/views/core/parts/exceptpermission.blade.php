<tr class="tr nivel_{{$nivel}} id_menu_item_{{$menu->id_menu_item}} id_parent_{{$menu->id_parent}}">
    <td class="">{{$menu->description or ''}}</td>
        <?php $btn_status = 0;?>

            @if(count($permissionsexceptions) > 0)
                @foreach($permissionsexceptions as $core_premissio)
                    @if(
                        !empty($core_premissio->id_user) && $core_premissio->id_user == $user->id_user
                        && $menu->id_menu_item == $core_premissio->id_menu_item
                    )

                        <?php $btn_status = 1;?>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="btn btn-xs app {{ !empty($core_premissio->permission) ? 'btn-success fas fa-check' : 'btn-danger fas fa-lock'}} "></i></span>
                                <input  type="text" class="form-control permission_exception_input" placeholder="Null" value="{{ $core_premissio->permission or  ''}}"
                                        data-id-perm-expt="{{$core_premissio->id_permission_exception}}"
                                        data-id-men-item="{{$menu->id_menu_item or 0}}"
                                        data-id-user="{{$user->id_user}}"
                                        data-perm="{{$core_premissio->permission or ''}}"
                                      >
                            </div>
                        </td>

                    @endif
                @endforeach
            @endif

            @if (empty($btn_status))
                <?php
        $group_id = $user->core_groups->id_group;
        $core_premission = \App\CorePermissions::where('id_group',$group_id)->where('id_menu_item',$menu->id_menu_item)->first();?>
                    <td>
                    <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="btn btn-xs btn-danger fas fa-lock"></i></span>
                        <input  type="text" class="form-control permission_exception_input isHanged" placeholder="Null"
                                data-id-men-item="{{$menu->id_menu_item or 0}}"
                                data-id-user="{{$user->id_user}}"
                                data-perm=""
                                value="{{$core_premission['permission']}}"

                        >
                    </div>

                </td>
            @endif


</tr>
@if (count($menu->children) > 0)
    <?php $nivel .= 0; ?>
    @foreach($menu->children as $menu)
        @include('core.parts.exceptpermission', ['menu' => $menu,'permissionsexceptions'=> $permissionsexceptions, 'nivel' => $nivel])
    @endforeach
@endif
