import { User } from '@/types/index';

export interface Message {
    id: number;
    user_id: number;
    team_id: number;
    message: string;
    created_at: string;
    updated_at: string;
    user: User;
}
