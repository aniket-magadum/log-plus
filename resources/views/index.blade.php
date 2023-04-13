@extends('statamic::layout')
@section('title', __('Log Viewer'))

@section('content')
    @if ($files)
        <div class="card p-2 my-2 flex flex-row justify-between">
            <div>
                <form method="GET" id="log-select-form">
                    <select name="file" id="log-file-select" class="p-1">
                        @foreach ($files as $key => $file)
                            <option value="{{ $key }}" @if ($key == request('file')) selected @endif>
                                {{ $key }} &nbsp;&nbsp; {{$file['displayable_size']}}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div>
                @if ($files)
                    <form action="{{route('statamic.cp.utilities.log-plus.delete',
                        ['file' => request('file') ?? array_key_first($files)])}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn-danger" type="submit">Delete File</button>
                    </form>        
                @endif
            </div>
        </div>
    @endif
    <div class="card p-0">
        @if (empty($files))
            <p class="text-red text-center p-2">No Log Files to Display</p>  
        @elseif($unique_logs->isEmpty())
             <p class="text-red text-center p-2">Log file is very large and cannot be viewed. Max File Size is {{$max_file_size/(1024*1024)}} MB</p>          
        @else
        <table class="border">
            <tr class="border">
                <th class="border p-1">Context</th>
                <th class="border p-1">Level</th>
                <th class="border p-1">Last Occured</th>
                <th class="border p-1">Message</th>
            </tr>

            <tbody>

                    @foreach ($unique_logs as $key => $logs)
                        <tr>
                            <td class="text-center text-sm" colspan="4">
                                <span><b>Total Occurrences</b> : {{ count($logs) }}</span>
                                <span><b>First Occurrence</b> : {{$logs->last()['date']}}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border p-1 text-center text-sm">{{ $logs[0]['context'] }}</td>
                            <td class="border p-1 text-center text-sm" style="color:{{$color_mappings[$logs[0]['level']] ?? 'black'}}">{{ $logs[0]['level'] }}</td>
                            <td class="border p-1 text-sm">{{ $logs[0]['date'] }}</td>
                            <td class="border p-1 text-sm">
                                <a onclick="showFullMessage('{{$key}}')">{{ str($logs[0]['text'])->limit(100) }}</a>
                            </td>
                        </tr>
                        <tr class="hidden" id="full-message-{{$key}}">
                            <td colspan="4" class="border border-2 p-1 w-full text-xs">
                                {{ $logs[0]['text'] }}
                            </td>
                        </tr>
                    @endforeach
            </tbody>
        </table>
        @endif
    </div>
@stop
