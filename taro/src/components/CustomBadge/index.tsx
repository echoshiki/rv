import { Tag, TagType } from '@nutui/nutui-react-taro';

interface StatusBadgeProps {
    status: {
        label: string,
        value: string
    }
}

const StatusBadge = ({ status }: StatusBadgeProps) => {
    let theme: TagType = 'default';
    switch (status.value) {
        case 'pending':
            theme = 'default';
            break;
        case 'approved':
            theme = 'success';
            break;
        case 'rejected':
            theme = 'warning';
            break;
        case 'cancelled':
            theme = 'danger';
            break;
        default:
            theme = 'default';
            break;
    }
    return <Tag type={theme}>{status.label}</Tag>
}

interface LevelBadgeProps {
    level: {
        id: number,
        name: string
    }   
}

const LevelBadge = ({ level }: LevelBadgeProps) => {
    let theme: TagType = 'default';
    
    switch (level.id) {
        case 1:
            theme = 'default';
            break;
        case 2:
            theme = 'success';
            break;
        case 3:
            theme = 'primary';
            break;
        case 4:
            theme = 'warning';
            break;
        case 5:
            theme = 'info';
            break;
        case 6:
            theme = 'danger';
            break;
        default:
            theme = 'default';
            break;
    }
    return <Tag type={theme}>{level.name}</Tag>
}

export {
    StatusBadge,
    LevelBadge
}