import { Player } from '@/types/Player';

export interface Event {
    id: number;
    team_id: number;
    type: string;
    occurs_at: string | null;
    location: string;
    details: string | null;
    players: Player[];
    attending_count: number;
    unavailable_count: number;
}
