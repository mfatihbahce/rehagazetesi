<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use App\Models\User;
use App\Models\VisitLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isEditor()) {
            return $this->editorDashboard($user);
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $stats = [
            'total_news' => News::count(),
            'draft_news' => News::where('status', 'draft')->count(),
            'pending_news' => News::where('status', 'pending')->count(),
            'published_news' => News::where('status', 'published')->count(),
            'total_editors' => User::where('role', 'editor')->count(),
            'total_categories' => Category::count(),
            'total_views' => News::where('status', 'published')->sum('views'),
        ];

        $mediaCount = $this->getMediaCount();

        $visitors = $this->getVisitorStats();
        $visitLogs = $this->getRecentVisitLogs();
        $dailyChart = $this->getDailyVisitorChart();
        $topPages = $this->getTopPages();

        $recentNews = News::with(['category', 'author'])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $popularNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(7)
            ->get();

        $pendingNews = News::with(['category', 'author'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        return view('admin.dashboard', [
            'isEditor' => false,
            'stats' => $stats,
            'recentNews' => $recentNews,
            'popularNews' => $popularNews,
            'pendingNews' => $pendingNews,
            'mediaCount' => $mediaCount,
            'visitors' => $visitors,
            'visitLogs' => $visitLogs,
            'dailyChart' => $dailyChart,
            'topPages' => $topPages,
        ]);
    }

    private function editorDashboard(User $user)
    {
        $baseQuery = News::where('user_id', $user->id);

        $stats = [
            'total_news' => (clone $baseQuery)->count(),
            'draft_news' => (clone $baseQuery)->where('status', 'draft')->count(),
            'pending_news' => (clone $baseQuery)->where('status', 'pending')->count(),
            'published_news' => (clone $baseQuery)->where('status', 'published')->count(),
            'total_views' => (clone $baseQuery)->sum('views'),
        ];

        $recentNews = News::where('user_id', $user->id)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        $popularNews = News::where('user_id', $user->id)
            ->where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(7)
            ->get();

        $mediaCount = $this->getMediaCount();

        $pendingNews = News::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        return view('admin.dashboard', [
            'isEditor' => true,
            'stats' => $stats,
            'recentNews' => $recentNews,
            'popularNews' => $popularNews,
            'pendingNews' => $pendingNews,
            'mediaCount' => $mediaCount,
            'visitors' => null,
            'visitLogs' => collect(),
            'dailyChart' => [],
            'topPages' => collect(),
        ]);
    }

    private function getVisitorStats(): array
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        $hitsToday = VisitLog::where('visited_at', '>=', $today)->count();
        $hitsWeek = VisitLog::where('visited_at', '>=', $weekStart)->count();
        $hitsTotal = VisitLog::count();

        $uniqueToday = (int) VisitLog::where('visited_at', '>=', $today)
            ->select(DB::raw('COUNT(DISTINCT ip) as c'))->value('c');
        $uniqueWeek = (int) VisitLog::where('visited_at', '>=', $weekStart)
            ->select(DB::raw('COUNT(DISTINCT ip) as c'))->value('c');
        $yesterday = now()->subDay()->startOfDay();
        $uniqueYesterday = (int) VisitLog::whereBetween('visited_at', [$yesterday, $today])
            ->select(DB::raw('COUNT(DISTINCT ip) as c'))->value('c');

        $percentChange = $uniqueYesterday > 0
            ? round((($uniqueToday - $uniqueYesterday) / $uniqueYesterday) * 100, 1)
            : 0;

        return [
            'hits_today' => $hitsToday,
            'hits_week' => $hitsWeek,
            'hits_total' => $hitsTotal,
            'unique_today' => $uniqueToday,
            'unique_week' => $uniqueWeek,
            'unique_total' => (int) VisitLog::select(DB::raw('COUNT(DISTINCT ip) as c'))->value('c'),
            'percent_change' => $percentChange,
        ];
    }

    private function getRecentVisitLogs()
    {
        return VisitLog::orderBy('visited_at', 'desc')
            ->limit(30)
            ->get()
            ->map(fn ($log) => [
                'path' => '/' . ltrim($log->path, '/'),
                'ip' => $this->maskIp($log->ip),
                'visited_at' => $log->visited_at,
            ]);
    }

    private function getDailyVisitorChart(): array
    {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $next = $date->copy()->addDay();
            $unique = (int) VisitLog::whereBetween('visited_at', [$date, $next])
                ->select(DB::raw('COUNT(DISTINCT ip) as c'))->value('c');
            $hits = VisitLog::whereBetween('visited_at', [$date, $next])->count();
            $days->push([
                'date' => $date->format('d.m'),
                'label' => $date->translatedFormat('D'),
                'unique' => $unique,
                'hits' => $hits,
            ]);
        }
        return $days->toArray();
    }

    private function getTopPages(int $limit = 10): \Illuminate\Support\Collection
    {
        return VisitLog::where('visited_at', '>=', now()->subDays(7))
            ->select('path', DB::raw('COUNT(*) as hits'), DB::raw('COUNT(DISTINCT ip) as unique_visitors'))
            ->groupBy('path')
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }

    private function maskIp(string $ip): string
    {
        if (str_contains($ip, ':')) {
            return '****:***' . substr($ip, -4);
        }
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.***.' . $parts[3];
        }
        return '***';
    }

    private function getMediaCount(): array
    {
        $images = 0;
        $videos = 0;
        $ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $vidExt = ['mp4', 'webm', 'ogg', 'mov'];

        foreach (['news', 'profiles', 'videos'] as $folder) {
            if (!Storage::disk('public')->exists($folder)) {
                continue;
            }
            foreach (Storage::disk('public')->allFiles($folder) as $path) {
                $e = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                if (in_array($e, $ext)) $images++;
                elseif (in_array($e, $vidExt)) $videos++;
            }
        }

        return ['images' => $images, 'videos' => $videos, 'total' => $images + $videos];
    }
}
