import { User } from '@/types/index';

export interface MessageAttachment {
    id: number;
    message_id: number;
    file_path: string;
    original_name: string;
    mime_type: string;
    size: number;
    url: string;
}

export interface Message {
    id: number;
    user_id: number;
    team_id: number;
    message: string;
    created_at: string;
    updated_at: string;
    user: User;
    attachments?: MessageAttachment[];
}
