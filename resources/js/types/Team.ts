import { Event } from '@/types/Event';
import { Player } from '@/types/Player';

export interface Team {
    id: number;
    name: string;
    age_group: string;
    players: Player[];
    events: Event[];
}
