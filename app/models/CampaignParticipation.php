<?php
/**
 * CampaignParticipation Model
 */
class CampaignParticipation extends Model
{
    protected string $table = 'campaign_participations';

    /**
     * Check if a member has already joined a campaign.
     */
    public function hasJoined(int $campaignId, int $memberId): bool
    {
        return (bool) $this->query(
            "SELECT COUNT(*) FROM campaign_participations
             WHERE campaign_id = ? AND member_id = ?",
            [$campaignId, $memberId]
        )->fetchColumn();
    }

    /**
     * Count how many members have joined a campaign.
     */
    public function getParticipantCount(int $campaignId): int
    {
        return (int) $this->query(
            "SELECT COUNT(*) FROM campaign_participations WHERE campaign_id = ?",
            [$campaignId]
        )->fetchColumn();
    }

    /**
     * Get all campaigns a member has joined, with campaign details.
     */
    public function getByMember(int $memberId): array
    {
        return $this->query(
            "SELECT cp.*, c.title, c.description, c.discount_pct, c.start_date,
                    c.end_date, c.status as campaign_status, c.banner_image,
                    CONCAT(ref.first_name, ' ', ref.last_name) as referred_by_name
             FROM campaign_participations cp
             JOIN campaigns c ON cp.campaign_id = c.id
             LEFT JOIN members ref ON cp.referred_by = ref.id
             WHERE cp.member_id = ?
             ORDER BY cp.joined_at DESC",
            [$memberId]
        )->fetchAll();
    }

    /**
     * Get all participants for a campaign (for admin/marketing view).
     */
    public function getByCampaign(int $campaignId): array
    {
        return $this->query(
            "SELECT cp.*, CONCAT(m.first_name, ' ', m.last_name) as member_name,
                    m.membership_id as member_code, cp.referral_code,
                    CONCAT(ref.first_name, ' ', ref.last_name) as referred_by_name
             FROM campaign_participations cp
             JOIN members m ON cp.member_id = m.id
             LEFT JOIN members ref ON cp.referred_by = ref.id
             WHERE cp.campaign_id = ?
             ORDER BY cp.joined_at DESC",
            [$campaignId]
        )->fetchAll();
    }

    /**
     * Find a participation by referral code.
     */
    public function findByReferralCode(string $code): array|false
    {
        return $this->query(
            "SELECT cp.*, CONCAT(m.first_name, ' ', m.last_name) as member_name
             FROM campaign_participations cp
             JOIN members m ON cp.member_id = m.id
             WHERE cp.referral_code = ?",
            [$code]
        )->fetch();
    }

    /**
     * Generate a unique referral code for a member + campaign.
     */
    public function generateReferralCode(int $campaignId, int $memberId): string
    {
        do {
            $code = strtoupper(substr(md5($campaignId . $memberId . uniqid()), 0, 8));
            $exists = $this->query(
                "SELECT COUNT(*) FROM campaign_participations WHERE referral_code = ?",
                [$code]
            )->fetchColumn();
        } while ($exists > 0);

        return $code;
    }
}
