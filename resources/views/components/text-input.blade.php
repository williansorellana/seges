@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-slate-700 bg-[#1e293b] text-slate-100 placeholder-slate-500 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
