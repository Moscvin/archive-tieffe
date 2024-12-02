<tr class="tr nivel_{{$nivel}} id_menu_item_{{$menu->id_menu_item}} id_parent_{{$menu->id_parent}}" >
    <td class="">{{$menu->description or ''}}</td>

    @foreach($groups as $group)
        <?php $btn_status = 0;?>
        <td>
            @if(count($menu->core_permission) > 0)
                @foreach($menu->core_permission as $core_premissio)
                    @if(
                        !empty($core_premissio->id_group) && $core_premissio->id_group == $group->id_group
                        && $menu->id_menu_item == $core_premissio->id_menu_item
                        &&  !empty($core_premissio->permission)
                    )<?php $btn_status = 1;?>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="btn btn-xs btn-{{ !empty($group->permission) ? 'danger' : 'success'}} fas fa-{{ !empty($group->permission) ? 'lock' : 'check'}}"></i></span>
                                <input  type="text" class="form-control permission_input" placeholder="Null" value="{{ $core_premissio->permission or  ''}}" data-l="{{ strlen($core_premissio->permission) }}"
                                       data-id="{{$core_premissio->id_permission or 0}}" data-group="{{$group->id_group or 0}}" data-menu="{{$menu->id_menu_item or 0}}">
                            </div>
                    @endif
                @endforeach
            @endif
            @if (empty($btn_status))
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="btn btn-xs btn-{{ empty($group->permission) ? 'danger' : 'success'}} fas fa-{{ empty($group->permission) ? 'lock' : 'check'}}"></i></span>
                        <input  type="text" class="form-control permission_input" value=""   placeholder="Null"
                               data-id="0" data-group="{{$group->id_group or 0}}" data-menu="{{$menu->id_menu_item or 0}}" >
                    </div>

            @endif

        </td>
    @endforeach
</tr>
@if (count($menu->children) > 0)

    <?php $nivel .= 0; ?>

    @foreach($menu->children as $menu)

        @include('core.parts.permission', ['menu' => $menu, 'groups' => $groups, 'nivel' => $nivel])

    @endforeach

@endif

