{{ Form::hidden('select_type', array_get($request, "select_type")) }}
{{ Form::hidden('select_property', array_get($request, "select_property")) }}
{{ Form::hidden('select_room', array_get($request, "select_room")) }}
{{ Form::hidden('value[Title]', array_get($request, "value.Title")) }}
{{ Form::hidden('value[Property]', array_get($request, "value.Property")) }}
{{ Form::hidden('value[Address]', array_get($request, "value.Address")) }}
{{ Form::hidden('value[Conditions]', array_get($request, "value.Conditions")) }}
{{ Form::hidden('value[Priority]', array_get($request, "value.Priority")) }}
{{ Form::hidden('value[Manager]', array_get($request, "value.Manager")) }}
{{ Form::hidden('value[FieldPersonnel]', array_get($request, "value.FieldPersonnel")) }}
{{ Form::hidden('value[FieldPersonnelTel]', array_get($request, "value.FieldPersonnelTel")) }}
{{ Form::hidden('value[Comment]', array_get($request, "value.Comment")) }}
@if ($request_type == "move")
{{ Form::hidden('value[Room_Number_move_from]', array_get($request, "value.Room_Number_move_from")) }}
{{ Form::hidden('value[Floor_Name_move_from]', array_get($request, "value.Floor_Name_move_from")) }}
{{ Form::hidden('value[PropertyColumn_move_to]', array_get($request, "value.PropertyColumn_move_to")) }}
{{ Form::hidden('value[Room_Number_move_to]', array_get($request, "value.Room_Number_move_to")) }}
{{ Form::hidden('value[Floor_Name_move_to]', array_get($request, "value.Floor_Name_move_to")) }}
{{ Form::hidden('value[organization_name_move_vender]', array_get($request, "value.organization_name_move_vender")) }}
{{ Form::hidden('value[Preferred_date_move]', array_get($request, "value.Preferred_date_move")) }}
{{ Form::hidden('value[Preferred_time_move]', array_get($request, "value.Preferred_time_move")) }}
{{ Form::hidden('value[Preferred_time_to]', array_get($request, "value.Preferred_time_to")) }}
{{ Form::hidden('value[memo_move]', array_get($request, "value.memo_move")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
@elseif ($request_type == "disposal")
{{ Form::hidden('value[Room_Number_move_from]', array_get($request, "value.Room_Number_move_from")) }}
{{ Form::hidden('value[Floor_Name_Disposal]', array_get($request, "value.Floor_Name_Disposal")) }}
{{ Form::hidden('value[organization_Disposal_vender]', array_get($request, "value.organization_Disposal_vender")) }}
{{ Form::hidden('value[Preferred_date_Disposal]', array_get($request, "value.Preferred_date_Disposal")) }}
{{ Form::hidden('value[Preferred_time_Disposal]', array_get($request, "value.Preferred_time_Disposal")) }}
{{ Form::hidden('value[Preferred_time_to]', array_get($request, "value.Preferred_time_to")) }}
{{ Form::hidden('value[memo_Disposal]', array_get($request, "value.memo_Disposal")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
@elseif ($request_type == "sale")
{{ Form::hidden('value[Room_Number_Sale]', array_get($request, "value.Room_Number_Sale")) }}
{{ Form::hidden('value[Floor_Name_Sale]', array_get($request, "value.Floor_Name_Sale")) }}
{{ Form::hidden('value[organization_Sale_vender]', array_get($request, "value.organization_Sale_vender")) }}
{{ Form::hidden('value[Preferred_date_Sale]', array_get($request, "value.Preferred_date_Sale")) }}
{{ Form::hidden('value[Preferred_time_Sale]', array_get($request, "value.Preferred_time_Sale")) }}
{{ Form::hidden('value[Preferred_time_to]', array_get($request, "value.Preferred_time_to")) }}
{{ Form::hidden('value[memo_Sale]', array_get($request, "value.memo_Sale")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
@elseif ($request_type == "change-classification")
{{ Form::hidden('value[Room_Number_Sale]', array_get($request, "value.Room_Number_Sale")) }}
{{ Form::hidden('value[Floor_Name_Change]', array_get($request, "value.Floor_Name_Change")) }}
@elseif ($request_type == "carry-in")
{{ Form::hidden('value[PropertyColumn_Carryin]', array_get($request, "value.PropertyColumn_Carryin")) }}
{{ Form::hidden('value[Room_Numbe_Carryin]', array_get($request, "value.Room_Numbe_Carryin")) }}
{{ Form::hidden('value[Floor_Name_Carryin]', array_get($request, "value.Floor_Name_Carryin")) }}
{{ Form::hidden('value[organization_Carryin_vender]', array_get($request, "value.organization_Carryin_vender")) }}
{{ Form::hidden('value[Preferred_date_Carryin]', array_get($request, "value.Preferred_date_Carryin")) }}
{{ Form::hidden('value[Preferred_time_Carryin]', array_get($request, "value.Preferred_time_Carryin")) }}
{{ Form::hidden('value[memo_Carryin]', array_get($request, "value.memo_Carryin")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
{{ Form::hidden('value[furniture_id_1]', array_get($request, "value.furniture_id_1")) }}
@elseif ($request_type == "carry-out")
{{ Form::hidden('value[PropertyColumn_Carryout]', array_get($request, "value.PropertyColumn_Carryout")) }}
{{ Form::hidden('value[Room_Numbe_Carryout]', array_get($request, "value.Room_Numbe_Carryout")) }}
{{ Form::hidden('value[Floor_Name_Carryout_from]', array_get($request, "value.Floor_Name_Carryout_from")) }}
{{ Form::hidden('value[organization_Carryout_vender]', array_get($request, "value.organization_Carryout_vender")) }}
{{ Form::hidden('value[Preferred_date_Carryout]', array_get($request, "value.Preferred_date_Carryout")) }}
{{ Form::hidden('value[Preferred_time_Carryout]', array_get($request, "value.Preferred_time_Carryout")) }}
{{ Form::hidden('value[memo_Carryout]', array_get($request, "value.memo_Carryout")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
@elseif ($request_type == "internal-move")
{{ Form::hidden('value[Room_Numbe_internalmove]', array_get($request, "value.Room_Numbe_internalmove")) }}
{{ Form::hidden('value[Floor_Name_internalmove_from]', array_get($request, "value.Floor_Name_internalmove_from")) }}
{{ Form::hidden('value[Room_Numbe_internalmove_to]', array_get($request, "value.Room_Numbe_internalmove_to")) }}
{{ Form::hidden('value[Floor_Name_internalmove_to]', array_get($request, "value.Floor_Name_internalmove_to")) }}
{{ Form::hidden('value[organization_inmove_vender]', array_get($request, "value.organization_inmove_vender")) }}
{{ Form::hidden('value[Preferred_date_internalmove]', array_get($request, "value.Preferred_date_internalmove")) }}
{{ Form::hidden('value[Preferred_time_internalmove]', array_get($request, "value.Preferred_time_internalmove")) }}
{{ Form::hidden('value[memo_internalmove]', array_get($request, "value.memo_internalmove")) }}
{{ Form::hidden('value[Estimate_only]', array_get($request, "value.Estimate_only")) }}
{{ Form::hidden('value[Enter_the_amount]', array_get($request, "value.Enter_the_amount")) }}
@endif
@if ($furniture_type == "soft")
    @if(array_get($request, "value.SoftFurniture"))
        @foreach(array_get($request, "value.SoftFurniture") as $id)
        {{ Form::hidden('value[SoftFurniture][]', $id) }}
        @endforeach
    @endif
@endif
@if ($furniture_type == "hard")
    @if ($request_type != "change-classification")
        {{ Form::hidden('value[Desk]', array_get($request, "value.Desk")) }}
        {{ Form::hidden('value[Chair]', array_get($request, "value.Chair")) }}
        {{ Form::hidden('value[Peds]', array_get($request, "value.Peds")) }}
    @endif
    @if ($request_type != "carry-in")
        {{ Form::hidden('value[DeskDefective]', array_get($request, "value.DeskDefective")) }}
        {{ Form::hidden('value[ChairDefective]', array_get($request, "value.ChairDefective")) }}
        {{ Form::hidden('value[PedsDefective]', array_get($request, "value.PedsDefective")) }}
    @endif
@endif