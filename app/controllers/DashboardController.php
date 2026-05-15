<?php
/**
 * DashboardController
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        AuthMiddleware::handle();

        $role       = Auth::role();
        $userId     = Auth::id();
        $notifModel = new Notification();

        // Base data for everyone
        $data = [
            'title'        => 'Dashboard',
            'unread_notifs'=> $notifModel->getUnreadCount($userId),
        ];

        // ── Gym Owner & Admin ────────────────────────────────────────────
        if (in_array($role, ['gym_owner', 'admin'])) {
            $memberModel     = new Member();
            $membershipModel = new Membership();
            $checkinModel    = new Checkin();
            $equipmentModel  = new Equipment();
            $bookingModel    = new Booking();
            $campaignModel   = new Campaign();

            $membershipModel->expireOldMemberships();

            $data += [
                'total_members'      => $memberModel->count(),
                'active_memberships' => $membershipModel->getActiveMembershipCount(),
                'today_checkins'     => $checkinModel->getTodayCount(),
                'monthly_revenue'    => $membershipModel->getMonthlyRevenue(),
                'total_revenue'      => $membershipModel->getTotalRevenue(),
                'equipment_status'   => $equipmentModel->getStatusCounts(),
                'expiring_soon'      => $membershipModel->getExpiringMemberships(7),
                'recent_checkins'    => $checkinModel->getTodayCheckins(),
                'upcoming_bookings'  => $bookingModel->getUpcoming(5),
                'recent_members'     => $memberModel->getRecentMembers(5),
                'active_campaigns'   => $campaignModel->getActiveCampaigns(),
                'weekly_checkins'    => $checkinModel->getWeeklyStats(),
                // pending membership applications
                'pending_memberships'=> (new Membership())->query(
                    "SELECT COUNT(*) FROM memberships WHERE status = 'pending'"
                )->fetchColumn(),
            ];
        }

        // ── Trainer ──────────────────────────────────────────────────────
        if ($role === 'trainer') {
            $employeeModel  = new Employee();
            $bookingModel   = new Booking();
            $fitnessPlanModel  = new FitnessPlan();
            $nutritionPlanModel = new NutritionPlan();

            $employee = $employeeModel->getByUserId($userId);
            $trainerId = $employee['id'] ?? null;

            $myBookings      = $trainerId ? $bookingModel->getByTrainer($trainerId) : [];
            $upcomingBookings = array_filter($myBookings, fn($b) =>
                $b['booking_date'] >= date('Y-m-d') && $b['status'] === 'scheduled'
            );
            $todayBookings = array_filter($myBookings, fn($b) =>
                $b['booking_date'] === date('Y-m-d')
            );

            // Count active fitness & nutrition plans assigned to this trainer
            $activeFitnessPlans = $trainerId ? (int)(new FitnessPlan())->query(
                "SELECT COUNT(*) FROM fitness_plans WHERE trainer_id = ? AND status = 'active'",
                [$trainerId]
            )->fetchColumn() : 0;

            $activeNutritionPlans = $trainerId ? (int)(new NutritionPlan())->query(
                "SELECT COUNT(*) FROM nutrition_plans WHERE trainer_id = ? AND status = 'active'",
                [$trainerId]
            )->fetchColumn() : 0;

            // Members assigned to this trainer (via fitness plans)
            $assignedMembers = $trainerId ? (new FitnessPlan())->query(
                "SELECT DISTINCT m.id, m.first_name, m.last_name, m.membership_id,
                        fp.plan_name, fp.status as plan_status, fp.created_at
                 FROM fitness_plans fp
                 JOIN members m ON fp.member_id = m.id
                 WHERE fp.trainer_id = ? AND fp.status = 'active'
                 ORDER BY m.first_name ASC",
                [$trainerId]
            )->fetchAll() : [];

            // Recent fitness plans
            $recentFitnessPlans = $trainerId ? (new FitnessPlan())->query(
                "SELECT fp.*, CONCAT(m.first_name, ' ', m.last_name) as member_name
                 FROM fitness_plans fp
                 JOIN members m ON fp.member_id = m.id
                 WHERE fp.trainer_id = ?
                 ORDER BY fp.created_at DESC LIMIT 5",
                [$trainerId]
            )->fetchAll() : [];

            $data += [
                'employee'             => $employee,
                'trainer_id'           => $trainerId,
                'today_bookings'       => array_values($todayBookings),
                'upcoming_bookings'    => array_values(array_slice($upcomingBookings, 0, 5)),
                'total_bookings'       => count($myBookings),
                'active_fitness_plans' => $activeFitnessPlans,
                'active_nutrition_plans' => $activeNutritionPlans,
                'assigned_members'     => $assignedMembers,
                'recent_fitness_plans' => $recentFitnessPlans,
            ];
        }

        // ── Maintenance Supervisor ───────────────────────────────────────
        if ($role === 'maintenance') {
            $equipmentModel    = new Equipment();
            $maintenanceModel  = new Maintenance();
            $employeeModel     = new Employee();

            $employee = $employeeModel->getByUserId($userId);

            $pendingReports    = $maintenanceModel->getPendingReports();
            $needingMaintenance = $equipmentModel->getNeedingMaintenance();
            $allReports        = $maintenanceModel->getAllWithDetails(10, 0);

            $statusCounts = (new Maintenance())->query(
                "SELECT status, COUNT(*) as count FROM maintenance_reports GROUP BY status"
            )->fetchAll();

            $data += [
                'employee'            => $employee,
                'total_equipment'     => $equipmentModel->getTotalCount(),
                'equipment_status'    => $equipmentModel->getStatusCounts(),
                'needing_maintenance' => $needingMaintenance,
                'pending_reports'     => $pendingReports,
                'recent_reports'      => $allReports,
                'report_status_counts'=> $statusCounts,
            ];
        }

        // ── Marketing Officer ────────────────────────────────────────────
        if ($role === 'marketing') {
            $campaignModel = new Campaign();

            $allCampaigns      = $campaignModel->findAll('created_at DESC', 10);
            $activeCampaigns   = $campaignModel->getActiveCampaigns();
            $upcomingCampaigns = $campaignModel->getUpcomingCampaigns();

            $statusCounts = $campaignModel->query(
                "SELECT status, COUNT(*) as count FROM campaigns GROUP BY status"
            )->fetchAll();

            $totalBudget = $campaignModel->query(
                "SELECT COALESCE(SUM(budget), 0) FROM campaigns WHERE status IN ('active','completed')"
            )->fetchColumn();

            $data += [
                'active_campaigns'   => $activeCampaigns,
                'upcoming_campaigns' => $upcomingCampaigns,
                'recent_campaigns'   => $allCampaigns,
                'campaign_status_counts' => $statusCounts,
                'total_campaign_budget'  => (float)$totalBudget,
            ];
        }

        // ── Member ───────────────────────────────────────────────────────
        if ($role === 'member') {
            $memberModel        = new Member();
            $membershipModel    = new Membership();
            $checkinModel       = new Checkin();
            $bookingModel       = new Booking();
            $fitnessPlanModel   = new FitnessPlan();
            $nutritionPlanModel = new NutritionPlan();
            $dietModel          = new DietaryLog();

            $member = $memberModel->getMemberByUserId($userId);
            $memberId = $member['id'] ?? null;

            $memberships    = $memberId ? $membershipModel->getByMemberId($memberId) : [];
            $activeMembership = array_values(array_filter($memberships, fn($m) => $m['status'] === 'active'))[0] ?? null;

            $recentCheckins = $memberId ? $checkinModel->getByMember($memberId, 5) : [];
            $myBookings     = $memberId ? $bookingModel->getByMember($memberId) : [];
            $upcomingBookings = array_values(array_filter($myBookings, fn($b) =>
                $b['booking_date'] >= date('Y-m-d') && $b['status'] === 'scheduled'
            ));

            $activeFitnessPlan   = $memberId ? $fitnessPlanModel->getActivePlan($memberId) : null;
            $activeNutritionPlan = $memberId ? $nutritionPlanModel->getActivePlan($memberId) : null;
            $todayDiet           = $memberId ? $dietModel->getByMember($memberId, date('Y-m-d')) : [];
            $todayCalories       = $memberId ? $dietModel->getDailyCalories($memberId, date('Y-m-d')) : 0;

            $data += [
                'member'               => $member,
                'member_id'            => $memberId,
                'active_membership'    => $activeMembership,
                'memberships'          => $memberships,
                'recent_checkins'      => $recentCheckins,
                'upcoming_bookings'    => $upcomingBookings,
                'active_fitness_plan'  => $activeFitnessPlan,
                'active_nutrition_plan'=> $activeNutritionPlan,
                'today_diet'           => $todayDiet,
                'today_calories'       => $todayCalories,
            ];
        }

        // ── User (no role yet) ───────────────────────────────────────────
        if ($role === 'user') {
            $raModel = new RoleApplication();
            $data += [
                'my_applications' => $raModel->getForUser($userId),
                'pending_app'     => $raModel->getPendingForUser($userId),
            ];
        }

        $this->view('dashboard.index', $data);
    }
}
