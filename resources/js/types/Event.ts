export interface Event {
    id: number;
    team_id: number;
    type: string;
    occurs_at: string;
    location: string;
    details: string | null;
}
