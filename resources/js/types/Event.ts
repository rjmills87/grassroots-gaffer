import { Player } from '@/types/Player';

export interface EventAttachment {
    id: number;
    event_id: number;
    file_path: string;
    original_name: string;
    mime_type: string;
    size: number;
    url: string;
}

export interface Event {
    id: number;
    team_id: number;
    type: string;
    starts_at: string;
    ends_at: string;
    location: string;
    details: string | null;
    players: Player[];
    attachments?: EventAttachment[];
    attending_count: number;
    unavailable_count: number;
}
