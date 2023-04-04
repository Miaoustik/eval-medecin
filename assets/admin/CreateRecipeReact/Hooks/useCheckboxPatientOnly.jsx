import {useCallback, useMemo, useState} from "react";

export default function () {

    const [state, setState] = useState(false)

    const handleChange = useCallback(() => {
        setState(s => !s)
    }, [])

    return useMemo(() => ({
        state,
        handleChange,
        setState
    }), [
        state,
        handleChange
    ])

}