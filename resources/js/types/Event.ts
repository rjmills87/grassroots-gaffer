import { Player } from '@/types/Player';

export interface Event {
    id: number;
    team_id: number;
    type: string;
    starts_at: string;
    ends_at: string;
    location: string;
    details: string | null;
    players: Player[];
    attending_count: number;
    unavailable_count: number;
}
