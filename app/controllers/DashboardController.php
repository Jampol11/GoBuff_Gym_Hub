<?php
/**
 * DashboardController
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        AuthMiddleware::handle();

        $memberModel      = new Member();
        $membershipModel  = new Membership();
        $checkinModel     = new Checkin();
        $equipmentModel   = new Equipment();
        $notifModel       = new Notification();
        $bookingModel     = new Booking();
        $campaignModel    = new Campaign();

        // Auto-expire memberships
        $membershipModel->expireOldMemberships();

        $data = [
            'title'               => 'Dashboard',
            'total_members'       => $memberModel->count(),
            'active_memberships'  => $membershipModel->getActiveMembershipCount(),
            'today_checkins'      => $checkinModel->getTodayCount(),
            'monthly_revenue'     => $membershipModel->getMonthlyRevenue(),
            'total_revenue'       => $membershipModel->getTotalRevenue(),
            'equipment_status'    => $equipmentModel->getStatusCounts(),
            'expiring_soon'       => $membershipModel->getExpiringMemberships(7),
            'recent_checkins'     => $checkinModel->getTodayCheckins(),
            'upcoming_bookings'   => $bookingModel->getUpcoming(5),
            'recent_members'      => $memberModel->getRecentMembers(5),
            'active_campaigns'    => $campaignModel->getActiveCampaigns(),
            'notifications'       => $notifModel->getForUser(Auth::id(), 5),
            'unread_notifs'       => $notifModel->getUnreadCount(Auth::id()),
            'weekly_checkins'     => $checkinModel->getWeeklyStats(),
        ];

        $this->view('dashboard.index', $data);
    }
}
