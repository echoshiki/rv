interface menuItemProps {
    title: string,
    icon: string,
    description?: string,
    link: string,
    onClick?: (item: menuItemProps) => void,
}

interface menuListProps {
    menuList: menuItemProps[]
}

export {
    menuItemProps,
    menuListProps
}