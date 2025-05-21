import { Tag } from '@nutui/nutui-react-taro';

interface StatusBadgeProps {
    status: {
        label: string,
        value: string
    }
}

const StatusBadge = ({ status }: StatusBadgeProps) => {
    switch (status.value) {
        case 'pending':
            return <Tag type="default">{status.label}</Tag>
        case 'approved':
            return <Tag type="success">{status.label}</Tag>
        case 'rejected':
            return <Tag type="danger">{status.label}</Tag>
        case 'cancelled':
            return <Tag type="danger">{status.label}</Tag>
        default:
            return <Tag type="default">{status.label}</Tag>
    }
}

export default StatusBadge;