import { Tag } from '@nutui/nutui-react-taro';

const Badge = ({ text }: { text: string }) => {
    const type = text === '已报名' ? 'success' : 'danger';

    return (
        <Tag type={type}>{text}</Tag>
    )
}

export default Badge;