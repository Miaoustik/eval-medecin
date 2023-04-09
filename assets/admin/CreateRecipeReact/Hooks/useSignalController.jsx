import {useEffect, useRef} from "react";

export default function () {
    const controllerRef = useRef(new AbortController())

    useEffect(() => {
        return () => {
            controllerRef.current.abort()
        }
    }, [])

    return controllerRef
}