<?php
/**
 * Campaign Model
 */
class Campaign extends Model
{
    protected string $table = 'campaigns';

    public function getActiveCampaigns(): array
    {
        return $this->query(
            "SELECT * FROM campaigns WHERE status = 'active'
               AND start_date <= CURDATE() AND end_date >= CURDATE()
             ORDER BY start_date ASC"
        )->fetchAll();
    }

    public function getUpcomingCampaigns(): array
    {
        return $this->query(
            "SELECT * FROM campaigns WHERE status = 'scheduled' AND start_date > CURDATE()
             ORDER BY start_date ASC"
        )->fetchAll();
    }

    public function searchCampaigns(string $keyword): array
    {
        return $this->search(['title', 'description', 'target_audience'], $keyword);
    }
}
