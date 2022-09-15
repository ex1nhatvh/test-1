@extends('exment_furniture_management_system::common.base')
@section('title', $title)
@section('content')
<div class="form-horizontal" id="report">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <tbody>
                    <tr>
                        <th class="title">棚卸表/Inventory table</th>
                        <th class="controls">
                            <a href="{{ $create_url }}" class="btn btn-default"><i class="fa fa-file"></i>&nbsp;新規作成/Create</a>
                        </th>
                    </tr>
                    @foreach($reports as $report)
                    <tr>
                        <td class="title">{{ $report->created_at->format('Y/m/d') }} 作成</td>
                        <td class="controls">
                            <a href="{{ \Exceedone\Exment\Model\File::getUrl($report->getValue('file')) }}" target="_blank" class="btn btn-default" title="ダウンロード/Download"><i class="fa fa-download"></i></a>
                            {{ Form::open(['url' => $delete_url, 'pjax-container', 'style' => 'display:inline-block']) }}
                            <button type="submit" value="{{ $report->id }}" class="btn btn-default" title="削除/Delete"><i class="fa fa-times-circle"></i></button>
                            {{ Form::hidden(
                                'report_id',
                                $report->id,
                            ) }}
                            {{ Form::close() }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection