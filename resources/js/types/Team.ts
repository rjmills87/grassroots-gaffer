import { Event } from '@/types/Event';
import { Player } from '@/types/Player';
import { Message } from './Message';

export interface Team {
    id: number;
    name: string;
    age_group: string;
    players: Player[];
    events: Event[];
    messages: Message[];  
}
