@extends('layouts.student')

@section('content')
<style>
    /* Futuristic Pass Styling */
    .cyber-pass-card {
        background: radial-gradient(circle at 100% 0%, rgba(204, 255, 0, 0.08) 0%, transparent 50%),
                    radial-gradient(circle at 0% 100%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                    var(--bg-surface);
        border: 1px solid var(--border-subtle);
        position: relative;
        overflow: hidden;
    }
    
    .barcode-line {
        background: currentColor;
        height: 24px;
        display: inline-block;
        margin-right: 2px;
    }

    /* Live pulse animation */
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(204, 255, 0, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(204, 255, 0, 0); }
    }
    .live-pulse {
        animation: pulseGlow 2s infinite;
    }
</style>

<div class="space-y-6" x-data="{
    selectedDay: '{{ now()->format('D') }}',
    timeOfDay: (function() {
        const h = new Date().getHours();
        return h < 12 ? 'Morning' : (h < 17 ? 'Afternoon' : 'Evening');
    })()
}">

    <!-- ==================== 1. HERO GREETING & STREAK CAPSULE ==================== -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-2">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 font-mono">
                    <i class="fas fa-bolt mr-1"></i> ACTIVE TERM
                </span>
                <span class="text-xs text-zinc-400 font-mono">
                    {{ $activeEnrollment->academicYear->year_name ?? '2026' }} &bull; {{ $activeEnrollment->term->term_name ?? 'Term 1' }}
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-zinc-900 dark:text-white font-sans leading-none">
                Good <span x-text="timeOfDay">Morning</span>, <br class="hidden sm:inline">{{ Auth::user()->name }}
            </h1>
        </div>

        <!-- Student Performance Metric -->
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-2xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-mono font-black text-sm shadow-md">
                    {{ number_format($passRate, 0) }}%
                </div>
                <div>
                    <span class="text-[9px] uppercase font-bold text-zinc-400 font-mono block">Pass Rate</span>
                    <span class="text-xs font-black text-zinc-900 dark:text-white">{{ number_format($averageMarks, 1) }}% GPA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 2. FUTURISTIC HOLOGRAPHIC DIGITAL PASS ==================== -->
    @if($student)
    <div class="cyber-pass-card rounded-4xl p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
            
            <div class="space-y-4">
                <div class="flex items-center space-x-3.5">
                    @if($student->profile_picture)
                        <img src="{{ Storage::url($student->profile_picture) }}" alt="Profile Photo"
                             class="w-14 h-14 rounded-2xl object-cover ring-2 ring-blue-500/40 shadow-lg flex-shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-xl font-black font-mono shadow-md flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-widest text-zinc-400 font-bold block">ACADEMIC CREDENTIAL</span>
                        <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">{{ Auth::user()->name }}</h2>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 font-mono">
                    <span class="px-3 py-1 rounded-xl bg-blue-600 text-white text-xs font-bold shadow-sm">
                        {{ $student->reg_number }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold">
                        {{ $student->programme }}
                    </span>
                    <span class="px-3 py-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs">
                        <i class="fas fa-hand-holding-usd text-blue-500 mr-1"></i> {{ $student->display_funding_source ?? 'Not Specified' }}
                    </span>
                </div>
            </div>

            <!-- Right: Simulated Barcode / Verified Chip -->
            <div class="sm:text-right space-y-2 flex flex-col sm:items-end justify-between">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-xs font-mono font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>ENROLLED &bull; ACTIVE</span>
                </div>
                
                <!-- Mock Digital Barcode -->
                <div class="pt-2 opacity-60 dark:opacity-80 text-zinc-900 dark:text-white select-none">
                    <span class="barcode-line w-1"></span>
                    <span class="barcode-line w-2"></span>
                    <span class="barcode-line w-0.5"></span>
                    <span class="barcode-line w-3"></span>
                    <span class="barcode-line w-1.5"></span>
                    <span class="barcode-line w-2"></span>
                    <span class="barcode-line w-0.5"></span>
                    <span class="barcode-line w-2.5"></span>
                    <span class="barcode-line w-1"></span>
                    <span class="barcode-line w-3"></span>
                    <span class="barcode-line w-0.5"></span>
                    <span class="barcode-line w-2"></span>
                    <p class="text-[9px] font-mono text-zinc-400 tracking-widest mt-1">SOPHISTICATE PASS 2026</p>
                </div>
            </div>

        </div>
    </div>
    @endif

    <!-- ==================== 3. 1-TAP NEXT-GEN LAUNCHPAD ==================== -->
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Launchpad</span>
            <span class="text-xs font-mono text-zinc-500 font-bold">6 Modules</span>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2.5 sm:gap-3 font-mono">
            <!-- 1. Courses -->
            <a href="{{ route('student.subjects.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Courses</span>
                <span class="text-[9px] text-zinc-400 mt-0.5 font-sans">{{ $totalSubjects }} Total</span>
            </a>

            <!-- 2. Quizzes -->
            <a href="{{ route('student.quizzes.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer border-blue-500/30 hover:border-blue-500">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-bolt"></i>
                </div>
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Quizzes</span>
                <span class="text-[9px] text-blue-600 dark:text-blue-400 mt-0.5 font-sans">Interactive</span>
            </a>

            <!-- 3. Results -->
            <a href="{{ route('student.results.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-10 h-10 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-simple"></i>
                </div>
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Grades</span>
                <span class="text-[9px] text-zinc-400 mt-0.5 font-sans">Ledger</span>
            </a>

            <!-- 4. Fees -->
            <a href="{{ route('student.fees.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Wallet</span>
                <span class="text-[9px] text-zinc-400 mt-0.5 font-sans">Statement</span>
            </a>

            <!-- 5. Messages (Advisory) -->
            <a href="{{ route('student.messages.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer relative">
                <div class="w-10 h-10 rounded-2xl bg-pink-500/10 text-pink-500 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-comment-dots"></i>
                </div>
                @if($unreadMessages > 0)
                    <span class="absolute top-3 right-4 w-3.5 h-3.5 rounded-full bg-rose-500 text-white text-[8px] font-black flex items-center justify-center font-mono animate-pulse">
                        {{ $unreadMessages }}
                    </span>
                @endif
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Advisory</span>
                <span class="text-[9px] text-zinc-400 mt-0.5 font-sans">Tutor Chat</span>
            </a>

            <!-- 6. Resources -->
            <a href="{{ route('student.resources.index') }}"
                class="cyber-card p-4 flex flex-col items-center justify-center text-center group cursor-pointer">
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center text-base group-hover:scale-110 transition-transform">
                    <i class="fas fa-folder-open"></i>
                </div>
                <span class="text-xs font-black text-zinc-900 dark:text-white mt-2">Library</span>
                <span class="text-[9px] text-zinc-400 mt-0.5 font-sans">Files</span>
            </a>
        </div>
    </div>

    <!-- ==================== 4. LIVE CLASS STAGE & DAILY TIMETABLE ==================== -->
    <div class="cyber-card p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-800">
            <div>
                <span class="text-xs font-mono uppercase font-bold text-zinc-400">Class Schedule</span>
                <h3 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Today's Lectures & Timetable</h3>
            </div>

            <!-- Weekday Selector Capsule -->
            @php $weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']; @endphp
            <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-zinc-100 dark:bg-zinc-800/90 font-mono text-xs">
                @foreach($weekDays as $day)
                    <button @click="selectedDay = '{{ $day }}'"
                        :class="selectedDay === '{{ $day }}' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white'"
                        class="px-3 py-1.5 rounded-xl font-bold transition">
                        {{ $day }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Schedule Items -->
        <div class="space-y-3">
            @forelse($allCurrentResults as $idx => $r)
                @php
                    $isNow = ($idx === 0);
                    $mockTimes = ['08:30 AM - 10:00 AM', '10:30 AM - 12:00 PM', '01:30 PM - 03:00 PM'];
                    $time = $mockTimes[$idx % count($mockTimes)];
                @endphp

                @if($isNow)
                    <!-- Active Live Card -->
                    <div class="p-5 rounded-3xl bg-zinc-900 text-white dark:bg-zinc-800/90 border border-zinc-700 relative overflow-hidden space-y-3">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-600 text-white flex items-center gap-1.5 font-mono live-pulse shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                NOW IN SESSION
                            </span>
                            <span class="text-xs font-mono font-bold text-zinc-400">{{ $time }}</span>
                        </div>

                        <div>
                            <h4 class="text-lg font-black tracking-tight text-white">
                                {{ $r->subject->name ?? 'Course Title' }}
                            </h4>
                            <p class="text-xs text-zinc-400 font-mono mt-0.5">
                                Room 104 &bull; Code: {{ $r->subject->code ?? 'N/A' }} ({{ $r->subject->credit_hours ?? 3 }} Credits)
                            </p>
                        </div>

                        <div class="pt-2 flex items-center justify-between border-t border-zinc-700/60 text-xs">
                            <span class="text-zinc-400 font-medium">Assigned Faculty Tutor</span>
                            <a href="{{ route('student.subjects.index') }}" class="font-bold text-[#ccff00] hover:underline">
                                Course Outline &rarr;
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Regular Class Row -->
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-zinc-400 transition">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-zinc-200 dark:bg-zinc-700/60 flex items-center justify-center font-mono font-bold text-xs">
                                {{ $r->subject->code ?? 'SUB' }}
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-zinc-900 dark:text-white">
                                    {{ $r->subject->name ?? 'Course Title' }}
                                </h4>
                                <p class="text-[11px] text-zinc-400 font-mono mt-0.5">{{ $time }}</p>
                            </div>
                        </div>
                        <a href="{{ route('student.subjects.index') }}" class="text-xs font-bold text-blue-500 hover:underline font-mono">
                            View Syllabus &rarr;
                        </a>
                    </div>
                @endif
            @empty
                <div class="p-12 text-center text-zinc-400 space-y-2">
                    <i class="fas fa-calendar-check text-3xl opacity-40"></i>
                    <p class="text-xs font-bold">No lectures scheduled for this day</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== 5. ADVISORY MESSAGES & RECENT ANNOUNCEMENTS ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Direct Advisory Messages Stream -->
        <div class="cyber-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-pink-500 animate-pulse"></span>
                    <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Tutor Advisory & Inquiries</span>
                </div>
                <a href="{{ route('student.messages.create') }}"
                    class="px-2.5 py-1 rounded-xl bg-pink-500/10 text-pink-500 hover:bg-pink-500/20 font-bold text-xs font-mono transition">
                    + New Msg
                </a>
            </div>

            @php
                $recentStudentMessages = \App\Models\Message::where('student_id', $student->id ?? 0)
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($recentStudentMessages->count() > 0)
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($recentStudentMessages as $msg)
                        <a href="{{ route('student.messages.show', $msg) }}" class="py-3 flex items-start justify-between gap-3 hover:opacity-80 transition block group">
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white group-hover:text-pink-500 transition-colors">
                                    {{ $msg->subject ?? 'Inquiry' }}
                                </h4>
                                <p class="text-[11px] text-zinc-400 line-clamp-1 font-mono">{{ $msg->message }}</p>
                            </div>
                            <span class="text-[10px] text-zinc-500 font-mono flex-shrink-0">
                                {{ $msg->created_at->diffForHumans() }}
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="pt-2 text-right">
                    <a href="{{ route('student.messages.index') }}" class="text-xs font-bold text-pink-500 hover:underline font-mono">
                        View All Messages ({{ $recentStudentMessages->count() }}) &rarr;
                    </a>
                </div>
            @else
                <div class="py-6 text-center text-zinc-400 space-y-2">
                    <i class="fas fa-comment-dots text-2xl opacity-40"></i>
                    <p class="text-xs font-mono">No active advisory chats</p>
                    <a href="{{ route('student.messages.create') }}" class="inline-block text-xs font-bold text-pink-500 hover:underline font-mono">
                        Start an Inquiry with a Tutor &rarr;
                    </a>
                </div>
            @endif
        </div>

        <!-- Academic Bulletins & Notifications -->
        <div class="cyber-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Announcements</span>
                <a href="{{ route('student.notifications.index') }}" class="text-xs font-bold text-blue-500 hover:underline font-mono">
                    View All &rarr;
                </a>
            </div>

            @if($recentNotifications->count() > 0)
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($recentNotifications as $notif)
                        <a href="{{ route('student.notifications.show', $notif) }}" class="py-3 flex items-start justify-between gap-3 hover:opacity-80 transition block group">
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white group-hover:text-blue-500 transition-colors">
                                    {{ $notif->title }}
                                </h4>
                                <p class="text-[11px] text-zinc-400 line-clamp-1 font-mono">{{ $notif->message }}</p>
                            </div>
                            <span class="text-[10px] text-zinc-500 font-mono flex-shrink-0">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-zinc-400 space-y-1">
                    <p class="text-xs font-mono">No unread announcements</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== 6. BENTO METRICS: GRADES & TUITION WALLET ==================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Recent Results -->
        <div class="cyber-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Recent Assessments</span>
                <a href="{{ route('student.results.index') }}" class="text-xs font-bold text-blue-500 hover:underline font-mono">
                    Full Ledger &rarr;
                </a>
            </div>

            @if($recentResults->count() > 0)
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($recentResults as $r)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white">{{ $r->subject->name ?? 'Course' }}</h4>
                                <span class="text-[10px] text-zinc-400 font-mono">{{ $r->exam_type == 'exam1' ? 'Exam 1' : 'Exam 2' }}</span>
                            </div>
                            <div class="flex items-center space-x-2 font-mono">
                                <span class="text-xs font-bold text-zinc-900 dark:text-white">{{ number_format($r->marks, 1) }}%</span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-300 dark:border-zinc-700">
                                    Grade {{ $r->grade }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-8 text-center text-zinc-400 space-y-1">
                    <p class="text-xs font-mono">No grades recorded for this term yet.</p>
                </div>
            @endif
        </div>

        <!-- Tuition Wallet Status -->
        @php
            $settledPercent = $totalFees > 0 ? round(($paidFees / $totalFees) * 100) : 100;
        @endphp
        <div class="cyber-card p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <span class="text-xs font-black uppercase tracking-wider text-zinc-400 font-mono">Tuition Wallet</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black font-mono {{ $balanceFees > 0 ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                        {{ $settledPercent }}% Settled
                    </span>
                </div>

                <div class="mt-4 space-y-1">
                    <span class="text-[10px] text-zinc-400 font-mono uppercase">Outstanding Balance</span>
                    <h3 class="text-2xl font-black font-mono tracking-tight text-zinc-900 dark:text-white">
                        {{ $balanceFees > 0 ? 'MK ' . number_format($balanceFees, 0) : 'MK 0.00' }}
                    </h3>
                </div>

                <!-- Sleek Track -->
                <div class="w-full h-2 rounded-full bg-zinc-200 dark:bg-zinc-800 mt-4 overflow-hidden">
                    <div class="h-full bg-zinc-900 dark:bg-[#ccff00] rounded-full transition-all duration-700" style="width: {{ $settledPercent }}%"></div>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between text-xs font-mono border-t border-zinc-200 dark:border-zinc-800">
                <span class="text-zinc-400">Total: MK {{ number_format($totalFees, 0) }}</span>
                <a href="{{ route('student.fees.index') }}" class="font-bold text-zinc-900 dark:text-white hover:underline">
                    Statement &rarr;
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
