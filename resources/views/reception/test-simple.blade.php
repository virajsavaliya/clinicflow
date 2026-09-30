@extends('layouts.app')
@section('title', 'Test Dashboard')

@section('content')
<div style="padding: 40px;">
    <h1>Simple Test</h1>
    <button onclick="testFunction()" style="padding: 20px; background: blue; color: white; border: none; font-size: 20px;">
        Click Me
    </button>
    <div id="result"></div>
</div>
@endsection

@section('scripts')
<script>
console.log('Script section loaded!');

function testFunction() {
    document.getElementById('result').innerHTML = 'IT WORKS!';
    alert('Function is defined and working!');
}

console.log('testFunction defined:', typeof testFunction);
</script>
@endsection
