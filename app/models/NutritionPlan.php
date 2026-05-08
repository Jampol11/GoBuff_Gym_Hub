<?php
/**
 * NutritionPlan Model
 */
class NutritionPlan extends Model
{
    protected string $table = 'nutrition_plans';

    public function getByMember(int $memberId): array
    {
        return $this->query(
            "SELECT np.*, CONCAT(e.first_name, ' ', e.last_name) as trainer_name
             FROM nutrition_plans np
             LEFT JOIN employees e ON np.trainer_id = e.id
             WHERE np.member_id = ?
             ORDER BY np.created_at DESC",
            [$memberId]
        )->fetchAll();
    }

    public function getActivePlan(int $memberId): array|false
    {
        return $this->query(
            "SELECT * FROM nutrition_plans WHERE member_id = ? AND status = 'active' LIMIT 1",
            [$memberId]
        )->fetch();
    }
}
